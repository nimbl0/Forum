<?php
include_once("Comment.php");
include_once("User.php");

$postId = $_POST["postId"];
$commentAuthor = User::getUserBySessionId();

$content = $_POST["commentField"];

Comment::create($postId, $commentAuthor->id, $content);

header("location: PostPage.php?id=" . $postId);