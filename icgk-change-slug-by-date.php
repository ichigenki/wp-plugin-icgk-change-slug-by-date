<?php
/*
Plugin Name: ICGK Change Slug By Date
Plugin URI: 
Description: 「投稿」など（'post','topic','news'）のスラッグを、新規作成時には現在日付(YYYYMMDD形式)にし、保存時には公開日付（YYYYMMDD-HHII形式）にする
Version: 1.0.1
Author: ICHIGENKI
Author URI: 
License: GPL2
*/

// 「投稿」「お知らせ」「ニュース」新規作成時のスラッグの初期値を現在日付(YYYYMMDD形式)にする
function set_slug_date() {
	// 投稿以外(固定ページなど)の場合は適用しない
	if ( 'post' == get_post_type() || 'topic' == get_post_type() || 'news' == get_post_type() ) {
		echo "<script>\n";
		echo "	jQuery(function(jQuery){ jQuery('#post_name').val(" . date_i18n('Ymd') . "); });\n";
		echo "</script>\n";
	}
}
add_action( 'admin_head-post-new.php','set_slug_date' );

// 「投稿」「お知らせ」「ニュース」保存時にスラッグを公開日付（YYYYMMDD-HHII形式）にする
function action_save_post( $post_id ) {
	if ( 'post' == get_post_type() || 'topic' == get_post_type() || 'news' == get_post_type() ) {
			// 自動保存時は何もしない
		if ( $parent_id = wp_is_post_revision( $post_id ) ) $post_id = $parent_id;
		// 無限ループを回避するためにこの関数をアンフックする
		remove_action( 'save_post', 'action_save_post' );
		// 投稿更新時にデータベースにある投稿を更新する
		wp_update_post( array( 'ID' => $post_id, 'post_name' => get_the_date( 'Ymd-Hi', $post_id ) ) );
		// re-hook this function
		// この関数を再フック
		add_action( 'save_post', 'action_save_post' );
	}
}
add_action( 'save_post' , 'action_save_post');

?>