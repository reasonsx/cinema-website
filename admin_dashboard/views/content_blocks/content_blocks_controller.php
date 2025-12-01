<?php

require_once __DIR__ . '/content_blocks_functions.php';

$success = '';
$error = '';

// ADD
if (isset($_POST['add_block'])) {
    [$success, $error] = addContentBlock($db, $_POST);
}

// EDIT
if (isset($_POST['edit_block'])) {
    [$success, $error] = editContentBlock($db, $_POST);
}

// DELETE
if (isset($_POST['delete_block'])) {
    [$success, $error] = deleteContentBlock($db, (int)$_POST['delete_block_id']);
}

