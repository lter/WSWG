<div>
    <a href="http://www.lternet.edu/">LTER Home</a>
    &bull;
    <a href="http://intranet2.lternet.edu/">Intranet</a>
</div>

<img src="<?php echo PDB_URL; ?>template/image/Personnel-logo.png" height="60" width="750" />



<div id="login">
  <?php if (isset($_SESSION['PDB']['LOGIN_MESSAGE'])) { ?>

  <span><?php echo $_SESSION['PDB']['LOGIN_MESSAGE']; ?></span>

  <?php } ?>

  <?php if (isLoggedIn()) { ?>

  <form method="POST" action="<?php echo PDB_SECURE_URL; ?>authenticate.php?action=deauthenticate">
  <button type="submit">Log Out</button>
  </form>

  <?php } else { ?>
  
  <form method="POST" action="<?php echo PDB_SECURE_URL; ?>authenticate.php?action=authenticate">
  <input type="text" name="username"/>
  <input type="password" name="password"/>
  <button type="submit">Log In</button>
  </form>

  <?php } ?>
</div>
