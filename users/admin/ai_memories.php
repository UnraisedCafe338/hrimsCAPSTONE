<?php
// Simple admin page to view and delete AI memories
require_once '../../handlers/connection.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>AI Memories</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .memory { border-bottom: 1px solid #eee; padding: 10px 0; }
    .controls { margin-top: 8px; }
    .btn { padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc; background: #f8f9fa; cursor: pointer; }
    .btn.danger { background: #ffdddd; border-color: #ffaaaa; }
  </style>
</head>
<body>
  <h1>AI Memories</h1>
  <p>Recent saved memories from the AI assistant.</p>

  <?php
  try {
      $collection = (new MongoDB\Client("mongodb://localhost:27017/"))->hrims_db->ai_memories;
      $cursor = $collection->find([], ['sort' => ['created_at' => -1], 'limit' => 200]);
      foreach ($cursor as $doc) {
          $text = htmlspecialchars($doc['text'] ?? '');
          $created = '';
          if (isset($doc['created_at']) && is_object($doc['created_at']) && method_exists($doc['created_at'],'toDateTime')) {
              $created = $doc['created_at']->toDateTime()->format(DATE_ATOM);
          } else if (isset($doc['created_at'])) {
              $created = (string)$doc['created_at'];
          }
          $id = (string)$doc['_id'];
          echo "<div class=\"memory\">";
          echo "<div><strong>" . $created . "</strong></div>";
          echo "<div>" . nl2br($text) . "</div>";
          echo "<div class=\"controls\"><button class=\"btn danger\" onclick=\"if(confirm('Delete memory?')){fetch('../../handlers/ai/delete_memory.php?id={$id}',{method:'POST'}).then(()=>location.reload())}\">Delete</button></div>";
          echo "</div>";
      }
  } catch (Exception $e) {
      echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
  }
  ?>

</body>
</html>
