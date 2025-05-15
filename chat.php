<?php
session_start();
include_once "php/config.php";
if (!isset($_SESSION['unique_id'])) {
  header("location: login.php");
}
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style/chat.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
</head>

<body class="outer-body">
  <div class="chat-wrapper">
    <section class="chat-area">
      <header>
        <?php
        $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
        $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$user_id}");
        if (mysqli_num_rows($sql) > 0) {
          $row = mysqli_fetch_assoc($sql);
        } else {
          header("location: users.php");
        }
        ?>
        <!-- <a href="/" class="back-icon"><i class="fas fa-arrow-left"></i></a> -->
        <div class="userprofile-container">
          <img src="php/images/<?php echo $row['img']; ?>" alt="">
          <div class="green-dot"></div>
        </div>
        <div class="details">
          <span><?php echo $row['fname'] . " " . $row['lname'] ?></span>
          <p><?php echo $row['status']; ?></p>
        </div>
        <!-- <?php if ($row['status'] === "Active now"): ?>
          <div class="online-status-container">
            Active now
          </div>
        <?php else: ?>
          <div class="offline-status-container">
            Offline
          </div>
        <?php endif; ?> -->
      </header>
      <div class="chat-box">

      </div>
      <form action="#" class="typing-area">
        <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>

        <div class="messageBox">
          <input placeholder="Message..." type="text" name="message" class="input-field" id="messageInput" autocomplete="off" />
          <button id="sendButton">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 664 663">
              <path
                fill="none"
                d="M646.293 331.888L17.7538 17.6187L155.245 331.888M646.293 331.888L17.753 646.157L155.245 331.888M646.293 331.888L318.735 330.228L155.245 331.888"></path>
              <path
                stroke-linejoin="round"
                stroke-linecap="round"
                stroke-width="33.67"
                stroke="#6c6c6c"
                d="M646.293 331.888L17.7538 17.6187L155.245 331.888M646.293 331.888L17.753 646.157L155.245 331.888M646.293 331.888L318.735 330.228L155.245 331.888"></path>
            </svg>
          </button>
        </div>

        <!-- <input type="text" name="message" class="input-field" placeholder="Message..." autocomplete="off">
        <button><i class="fab fa-telegram-plane"></i></button> -->
      </form>
    </section>
  </div>

  <script src="javascript/chat.js"></script>

</body>

</html>