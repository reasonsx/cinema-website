<?php
require_once __DIR__ . "/content_blocks_functions.php";

$action = $_POST['action'] ?? '';

switch ($action) {

    case 'create':
        $tag = trim($_POST['tag']);
        $title = trim($_POST['title'] ?? '');
        $text = trim($_POST['text'] ?? '');

        if (empty($tag)) {
            $error = "Tag cannot be empty.";
        } elseif (createContentBlock($db, $tag, $title, $text)) {
            $success = "Content block created.";
        } else {
            $error = "Failed to create content block.";
        }
        break;

    case 'update':
        $id = (int)$_POST['id'];
        $tag = trim($_POST['tag']);
        $title = trim($_POST['title'] ?? '');
        $text = trim($_POST['text'] ?? '');

        if (updateContentBlock($db, $id, $tag, $title, $text)) {
            $success = "Content block updated.";
        } else {
            $error = "Failed to update content block.";
        }
        break;

    case 'delete':
        $id = (int)$_POST['id'];
        if (deleteContentBlock($db, $id)) {
            $success = "Content block deleted.";
        } else {
            $error = "Failed to delete content block.";
        }
        break;
}
