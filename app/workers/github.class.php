<?php
namespace mione\workers;
use \mione\config\Database as DB;

class Github extends Worker{
  // public function run(){
  //   $open_prs = $this->get_open_prs();
  //   $filtered = [];
  //   foreach( $open_prs as $pr ){
  //     $remote_pr = $this->get_pr($pr->number);
  //     $mergeable = $remote_pr->mergeable ?? null;
  //     array_push( $filtered, [
  //       'pr_number' => $pr->number,
  //       'mergeable' => $mergeable,
  //       'user' => $pr->user->login ?? 'unknown',
  //     ] );
  //   }
  //   return $filtered;
  // }

  public function run(){
    $clock_in = \DateTime::createFromFormat('H:i', '08:00');
    $clock_out = \DateTime::createFromFormat('H:i', '20:00');
    $now = \DateTime::createFromFormat('H:i', date('H:i'));
    if( $clock_in > $now || $now > $clock_out ) {
      return "I only work between {$clock_in->format('H:i')} and {$clock_out->format('H:i')}";
    }

    $sql = 'SELECT * FROM hooks WHERE service = :service AND status = :status';
    $stmt = DB::prepare( $sql );
    $stmt->execute( [ ':service' => 'github', ':status' => 'created' ] );
    $rows = $stmt->fetchAll( \PDO::FETCH_OBJ );
    // $filtered = [];
    foreach( $rows as &$row ){
      $payload = (object) json_decode( $row->payload );
      // $row->payload = $payload;
      $repository = $payload->repository->full_name ?? false;
      $repository_data = ENV['REPOSITORIES'][$repository] ?? false;
      $discord_webhook = $repository_data['DISCORD_CHANNEL_WEBHOOK'] ?? false;

      if( $repository_data && $discord_webhook ){
        $action = $payload->action ?? false;
        $ref = $payload->ref ?? false;
        $pull_request = $payload->pull_request ?? false;
        $merged = $pull_request->merged ?? null;
        $sender = $payload->sender->login ?? false;
        $review = $payload->review ?? false;
        unset( $row->payload );
        $row->action = $action;
        $row->ref = $ref;
        $row->repository = $repository;
        $row->sender = $sender;

        if( $ref == 'refs/heads/master' || $ref == 'refs/heads/production' ){
          $branch = str_replace( 'refs/heads/', '', $ref );
          $row->status = 'processed';
          $row->discord_webhook = $discord_webhook;
          $commits = count( $payload->commits );
          $row->message = "@here {$row->sender} mergeou {$commits} commits na `{$branch}`:\n";
          foreach( $payload->commits as $commit ){
            $row->message .= "- {$commit->message};\n";
          }
          if( strlen( $row->message ) > 2000 ){
            $row->message = substr( $row->message, 0, 1997 ) . '...';
          }

          $this->post_message( $discord_webhook, $row->message );
          $this->update_status( $row->id, 'processed' );
          
          continue;
        }

        // if( $action == 'closed' && $pull_request && $merged == true ){
          // $row->message = "@here PR XXX was merged";
          // continue;
        // }

      }
      $this->update_status( $row->id, 'ignored' );
    }
    return $rows;
  }

  public function update_status( $id, $status ){
    $sql = 'UPDATE hooks SET status = :status, updated_at = :updated_at WHERE id = :id';
    $stmt = DB::prepare( $sql );
    $stmt->execute( [ ':id' => $id, ':status' => $status, 'updated_at' => date('Y-m-d H:i:s') ] );
  }

  public function post_message( $url, $message ){
    $json = json_encode(['content'=> $message]);
    $ch = curl_init( $url );
    curl_setopt_array( $ch, [
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json)
      ],
      CURLOPT_POSTFIELDS => $json,
      CURLOPT_RETURNTRANSFER => true,
    ] );
    curl_exec($ch);
  }

}
