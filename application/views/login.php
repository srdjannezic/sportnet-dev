<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
   <title>SportNet CMS Login</title>
   <link rel="stylesheet" href="<?= base_url(); ?>assets/css/bootstrap.min.css"/>
   <link rel="stylesheet" href="<?= base_url(); ?>assets/css/main.css"/>
 </head>
 <body class="login">
 <div class="container">
 <div class="login-wrapper">
   <h1>SportNet CMS Login</h1>
   <?php echo validation_errors(); ?>
   <?php echo form_open('verifylogin',array("class"=>"login-form")); ?>
   <div class="form-group">
     <label for="username">Username:</label>
     <input type="text" size="20" class="form-control" id="username" name="username"/>
   </div>
   <div class="form-group">
     <label for="password">Password:</label>
     <input type="password" size="20" class="form-control" id="password" name="password"/>
   </div>
     <input type="submit" class="btn btn-default" value="Login"/>

   </form>
  </div>
  </div>
 </body>
</html>