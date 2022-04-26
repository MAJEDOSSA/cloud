<?php
session_start();
$error_message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_name']) && $_POST['form_name'] == 'changepassword')
{
   $database = './usersdb.php';
   $success_page = '';
   if (!isset($_SESSION['username']))
   {
      $error_message = 'Not logged in!';
   }
   else
   if (filesize($database) == 0)
   {
      $error_message = 'User database not found!';
   }
   else
   {
      $password_value = md5($_POST['password']);
      $newpassword = md5($_POST['newpassword']);
      $confirmpassword = md5($_POST['confirmpassword']);
      $username_value = $_SESSION['username'];
      if ($_POST['newpassword'] != $_POST['confirmpassword'])
      {
         $error_message = 'The confirm new password must match the new password entry';
      }
      else
      if (!preg_match("/^[A-Za-z0-9-_!@$]{1,50}$/", $_POST['newpassword']))
      {
         $error_message = 'New password is not valid, please check and try again!';
      }
      else
      {
         $items = file($database, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
         foreach($items as $line)
         {
            list($username, $password) = explode('|', trim($line));
            if ($username_value == $username)
            {
               if ($password_value != $password)
               {
                  $error_message = 'Old password is not valid!';
                  break;
               }
            }
         }
         if (empty($error_message))
         {
            $file = fopen($database, 'w');
            foreach($items as $line)
            {
               $values = explode('|', trim($line));
               if ($username_value == $values[0])
               {
                  $values[1] = $newpassword;
                  $line = '';
                  for ($i=0; $i < count($values); $i++)
                  {
                     if ($i != 0)
                        $line .= '|';
                     $line .= $values[$i];
                  }
               }
               fwrite($file, $line);
               fwrite($file, "\r\n");
            }
            fclose($file);
            header('Location: '.$success_page);
            exit;
         }
      }
   }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Page</title>
<meta name="generator" content="WYSIWYG Web Builder 17 Trial Version - https://www.wysiwygwebbuilder.com">
<link href="Untitled1.css" rel="stylesheet">
<link href="change_password.css" rel="stylesheet">
</head>
<body>
<div id="container">
<a href="https://www.wysiwygwebbuilder.com" target="_blank"><img src="images/builtwithwwb17.png" alt="WYSIWYG Web Builder" style="position:absolute;left:441px;top:967px;margin: 0;border-width:0;z-index:250" width="16" height="16"></a>
<div id="wb_ChangePassword1" style="position:absolute;left:508px;top:223px;width:306px;height:266px;z-index:1;">
<form name="changepasswordform" method="post" accept-charset="UTF-8" action="<?php echo basename(__FILE__); ?>" id="changepasswordform">
<input type="hidden" name="form_name" value="changepassword">
<table id="ChangePassword1">
<tr>
   <td class="header">Change your password</td>
</tr>
<tr>
   <td class="label"><label for="password">Password</label></td>
</tr>
<tr>
   <td class="row"><input class="input" name="password" type="password" id="password"></td>
</tr>
<tr>
   <td class="label"><label for="newpassword">New Password</label></td>
</tr>
<tr>
   <td class="row"><input class="input" name="newpassword" type="password" id="newpassword"></td>
</tr>
<tr>
   <td class="label"><label for="confirmpassword">Confirm New Password</label></td>
</tr>
<tr>
   <td class="row"><input class="input" name="confirmpassword" type="password" id="confirmpassword"></td>
</tr>
<tr>
   <td><?php echo $error_message; ?></td>
</tr>
<tr>
   <td style="text-align:center;vertical-align:bottom"><input class="button" type="submit" name="changepassword" value="Change Password" id="changepassword"></td>
</tr>
</table>
</form>
</div>
</div>
</body>
</html>