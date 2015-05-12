<?php
/**
 * Plugin Name: Elit Email New Comments
 * Description: Email a list of people when a new comment is posted
 * Version: 1.0.0
 * Author: Patrick Sinco
 * License: GPL2
 */


/*
 * Alert addressee by email whenever a new, nonspam comment is posted
 */
function elit_email_new_comment( $comment_id, $approval_status ) {

  if ( $approval_status && $approval_status != 'spam' ) {

    // prep contents of email
    $subject = 'A new comment has been posted on The DO';

    $comment = get_comment( $comment_id );
    $headline = get_post_field( 'post_title', $comment->comment_post_id );
    $msg = "Mark as spam: " . 
      add_query_arg(
        array(
          'action' => 'spam', 
          'c' => $comment_id
        ),
        admin_url( 'comment.php' )
      ) . PHP_EOL . PHP_EOL;

    $msg .= "Author:\t" . $comment->comment_author;
    $msg .= $comment->comment_author_url ? ( PHP_EOL . "Author URL: " . 
      $comment->comment_author_url . PHP_EOL ) : PHP_EOL;
    $msg .= PHP_EOL;
    $msg .= "Comment:\t" . PHP_EOL;
    $msg .= $comment->comment_content . PHP_EOL . PHP_EOL;
    $msg .= "Story:\t" . $headline . PHP_EOL;
    $msg .= get_permalink( $comment->comment_post_id );

    // for testing only
    //$msg .= 'Approval status: ' . $approval_status;

    // prep mimetype 
    $headers = 'Content-Type: text/plain' . "\r\n";

    $recips = array(
      'psinco@osteopathic.org',
      'bjohnson@osteopathic.org',
//      'rraymond@osteopathic.org',
      get_option( 'admin_email' )
    );

    foreach ( $recips as $recip ) {
      wp_mail( $recip, $subject, $msg, $headers );
    }
  }
}
add_action( 'comment_post', 'elit_email_new_comment', 10, 2 );
