<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.4/flowbite.min.css" rel="stylesheet" />
  <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.1.min.js"></script>
  <title>ChatApp — Developed by Mahfuj Ansari</title>
  <meta name="description" content="A secure, private and fast web chat application. Developed by Mahfuj Ansari.">
  <meta name="author" content="Mahfuj Ansari">
  <style>
    :root {
      --primary: #0f172a;
      --accent: #06b6d4;
    }
    body { background-color: var(--primary); }
  </style>
</head>

<body>
<?php
function nospaces($t){ return preg_replace('/\s/', '', $t); }
?>
