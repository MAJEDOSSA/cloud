<?php
session_start();
if (!isset($_SESSION['username']))
{
   $accessdenied_page = '';
   header('Location: '.$accessdenied_page);
   exit;
}
$database = './usersdb.php';
if (filesize($database) == 0)
{
   die('User database not found!');
}
$error_message = '';
$db_username = '';
$db_fullname = '';
$db_email = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_name']) && $_POST['form_name'] == 'editprofileform')
{
   $success_page = '';
   $oldusername = $_SESSION['username'];
   $newusername = $_POST['username'];
   $newemail = $_POST['email'];
   $newpassword = $_POST['password'];
   $confirmpassword = $_POST['confirmpassword'];
   $newfullname = $_POST['fullname'];
   if ($newpassword != $confirmpassword)
   {
      $error_message = 'Password and Confirm Password are not the same!';
   }
   else
   if (!preg_match("/^[A-Za-z0-9-_!@$]{1,50}$/", $newusername))
   {
      $error_message = 'Username is not valid, please check and try again!';
   }
   else
   if (!empty($newpassword) && !preg_match("/^[A-Za-z0-9-_!@$]{1,50}$/", $newpassword))
   {
      $error_message = 'Password is not valid, please check and try again!';
   }
   else
   if (!preg_match("/^[A-Za-z0-9-_!@$.' &]{1,50}$/", $newfullname))
   {
      $error_message = 'Fullname is not valid, please check and try again!';
   }
   else
   if (!preg_match("/^.+@.+\..+$/", $newemail))
   {
      $error_message = 'Email is not a valid email address. Please check and try again.';
   }
   else
   {
      $items = file($database, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      if ($oldusername != $newusername)
      {
         foreach($items as $line)
         {
            list($username) = explode('|', trim($line));
            if ($newusername == $username)
            {
               $error_message = 'Username already used. Please select another username.';
                break;
            }
         }
      }
      if (empty($error_message))
      {
         $file = fopen($database, 'w');
         foreach($items as $line)
         {
            list($username, $password) = explode('|', trim($line));
            if ($oldusername == $username)
            {
               $line = $newusername;
               $line .= "|";
               if (empty($newpassword))
               {
                  $line .= $password;
               }
               else
               {
                  $line .= md5($newpassword);
               }
               $line .= "|";
               $line .= $newemail;
               $line .= "|";
               $line .= $newfullname;
               $line .= "|1|";
            }
            fwrite($file, $line);
            fwrite($file, "\r\n");
         }
         fclose($file);
         $_SESSION['username'] = $newusername;
         $_SESSION['fullname'] = $newfullname;
         header('Location: '.$success_page);
         exit;
      }
   }
}
$items = file($database, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach($items as $line)
{
   list($username, $password, $email, $name, $active, $code) = explode('|', trim($line));
   if ($username == $_SESSION['username'])
   {
      $db_username = $username;
      $db_fullname = $name;
      $db_email = $email;
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
<link href="edit_profile.css" rel="stylesheet">
</head>
<body>
<div id="container">
<a href="https://www.wysiwygwebbuilder.com" target="_blank"><img src="images/builtwithwwb17.png" alt="WYSIWYG Web Builder" style="position:absolute;left:441px;top:967px;margin: 0;border-width:0;z-index:250" width="16" height="16"></a>
<div id="wb_EditAccount1" style="position:absolute;left:529px;top:197px;width:277px;height:396px;z-index:1;">
<form name="editprofileform" method="post" accept-charset="UTF-8" action="<?php echo basename(__FILE__); ?>" id="editprofileform">
<input type="hidden" name="form_name" value="editprofileform">
<table id="EditAccount1">
<tr>
   <td class="header">Edit Account</td>
</tr>
<tr>
   <td class="label"><label for="fullname">Full Name</label></td>
</tr>
<tr>
   <td class="row"><input class="input" name="fullname" type="text" id="fullname" value="<?php echo $db_fullname; ?>"></td>
</tr>
<tr>
   <td class="label"><label for="username">User Name</label></td>
</tr>
<tr>
   <td class="row"><input class="input" name="username" type="text" id="username" value="<?php echo $db_username; ?>"></td>
</tr>
<tr>
   <td class="label"><label for="password">Password</label></td>
</tr>
<tr>
   <td class="row"><input class="input" name="password" type="password" id="password"></td>
</tr>
<tr>
   <td class="label"><label for="confirmpassword">Confirm Password</label></td>
</tr>
<tr>
   <td class="row"><input class="input" name="confirmpassword" type="password" id="confirmpassword"></td>
</tr>
<tr>
   <td class="label"><label for="email">E-mail</label></td>
</tr>
<tr>
   <td class="row"><input class="input" name="email" type="text" id="email" value="<?php echo $db_email; ?>"></td>
</tr>
<tr>
   <td><?php echo $error_message; ?></td>
</tr>
<tr>
   <td style="text-align:center;vertical-align:bottom"><input class="button" type="submit" name="update" value="Update" id="update"></td>
</tr>
</table>
</form>
</div>
</div>
</body>
</html>