  <?php

// importing mail classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
require "vendor/autoload.php";
require_once('db.php');
//assigining the $conn variable to the $db variable
$db= $conn;

$contact_us= '';
$nameErr=$emailErr=$phoneErr=$subjectErr=$msgErr=false;

// set empty input value into the contact field
$set_name=$set_phone=$set_email=$set_subject=$set_msg='';




  if(isset($_POST['contact']))
  { 
    
          //Regular expressions for validation
          $validName="/^[a-zA-Z ]*$/"; // full Name
          $validEmail="/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/"; // Email
          $validPhone='/^\d{10}$/';//phone_no
    

          // server side form validations....

          //Full Name Validation
          if(empty($_POST['full_name'])){
            $nameErr="Full Name is Required"; 
          }
          else if (!preg_match($validName,$_POST['full_name'])) {
            $nameErr="Only Characters and white spaces are allowed";
          }
          else{
            $set_fullname= $_POST['full_name'];
            
          }

          //Email Address Validation
          if(empty($_POST['email'])){
            $emailErr="Email is Required"; 
          }
          else if (!preg_match($validEmail,($_POST['email']))) {
            $emailErr="Invalid Email Address";
          } 
          else{
            $set_email= $_POST['email'];
          }

          //phone  Validation
          if(empty($_POST['phone'])){
              $phoneErr="phone no is Required"; 
          }
          elseif(!preg_match($validPhone, $_POST['phone']))
          {
            $phoneErr="Phone No must container 10 digita";
          }
          else{
            $set_phone= $_POST['phone'];
            }

          //Subject Name Validation
          if(empty($_POST['subject'])){
            $subjectErr="Subject is Required"; 
          }else{
            $set_subject= $_POST['subject'];

          }
              
          //message Validation
          if(empty($_POST['msg'])){
            $msgErr="Message is Required"; 
          }else{
            $set_msg= $_POST['msg'];

          }
  



  // check all fields are valid or not
  if( !$nameErr && !$emailErr && !$phoneErr && !$subjectErr && !$msgErr){

                //sanitise and validate the inputs
                $fullName=  legal_input( $set_fullname);
                $emailAddress=  legal_input( $set_email);
                $phone=  legal_input($set_phone);
                $subject=  legal_input($set_subject);
                $message=  legal_input($set_msg);


                // call function to store contact message
                store_message($fullName,$emailAddress,$phone,$subject,$message);
                //call function to send the contact message
                $contact_us=send_mail($fullName,$emailAddress,$phone,$subject,$message);

          
              
        }
        else {
                    

                  $set_name    = $_POST['full_name'];
                  $set_email   = $_POST['email'];
                  $set_phone  = $_POST['phone'];
                  $set_subject = $_POST['subject'];
                  $set_msg     = $_POST['msg'];
            }
        }
  // convert illegal input value to legal value formate
        function legal_input($value) {
          $value = trim($value);
          $value = stripslashes($value);
          $value = htmlspecialchars($value);
          return $value;
        }


  // function to send mail to the website owner
  function send_mail($fullName, $emailAddress, $phone, $subject, $message) {
      

              $mail = new PHPMailer(true);

              // Set up the SMTP configuration
              $mail->isSMTP();
              $mail->Host = 'smtp.gmail.com'; 
              $mail->SMTPAuth = true;
              $mail->Username = 'swamybittu649@gmail.com'; 
              $mail->Password = 'fjzxohxamdpycqdi'; 
              $mail->SMTPSecure = 'tls';
              $mail->Port = 587; 

              try {
              // Set up the email details
              $mail->setFrom($emailAddress, $fullName);
              $mail->addAddress('swamybittu649@gmail.com'); // Replace with the recipient's email address
              $mail->Subject = 'Contact Message was sent by ' . $fullName;
              $mail->isHTML(true);
              $mail->Body = '<h2>Contact Message Details</h2>
                            <h3>Full Name</h3>
                            <p>' . $fullName . '</p>
                            <h3>Email Address</h3>
                            <p>' . $emailAddress . '</p>
                            <h3>Phone</h3>
                            <p>' . $phone . '</p>
                            <h3>Subject</h3>
                            <p>' . $subject . '</p>
                            <h3>Message</h3>
                            <p>' . $message . '</p>';

              // Send the email
              if ($mail->send()) {

                // storing the receipient in the session

                session_start();

                $_SESSION['recipientEmail'] ='swamybittu649@gmail.com';


                header('Location: success.php');

                exit();
                  
              } else {
                  return 'Message is unable to send, please try again. Error: ' . $mail->ErrorInfo;
              }
            }catch(Exception $e){
              return 'Message is unable to send,please try again.Error'. $mail->ErrorInfo;
            }
          }



  // function to insert user data into database table
  function store_message($fullName,$emailAddress, $phone,$subject,$message){

              global $db;
              $sql="INSERT INTO contact_form (fullname,email,phone,subject,Message,ip_address) VALUES(?,?,?,?,?,?)";
              
              // check if all required fields are required

              if(!empty($fullName) && !empty($emailAddress)&& !empty($phone)&& !empty($subject)&& !empty($message)){

              $ipAddress = $_SERVER['REMOTE_ADDR'];

              $query=$db->prepare($sql); //to prevent sql injection

              $query->bind_param('ssssss',$fullName,$emailAddress,$phone,$subject,$message,$ipAddress);
              
              $exec= $query->execute();
              
                
                }
            }


  ?>


  <!-- // Contact_us form.... -->
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <title>PHP Contact Form</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
    <!--bootstrap4 library linked-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <!--custom style-->
  <style type="text/css">
      .contact-form{
        background: #f7f7f7;
        padding: 20px;
        border: 1px solid orange;
        margin: 50px 0px;
      }
      .err-msg{
        color:red;
      }
      .contact-form form{
        border: 1px solid #e8e8e8;
        padding: 10px;
        background: #f3f3f3;
      }
  </style>
  </head>
  <body>
  <div class="container-fluid">
  <div class="row">
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
      
      <!--====contact us  form====-->
      <div class="contact-form">
        <h4 class="text-center">PHP Contact Form</h4>
        <p class="text-success text-center"><?php echo $contact_us; ?></p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"  method="post">  
        
          <!-- FullName -->
          <div class="form-group">
              <label>Full Name:</label>
              <input type="text" class="form-control" placeholder="Enter Full Name" name="full_name" value="<?php echo $set_name;?>">
              <p class="err-msg">
              <?php if($nameErr!=1){ echo $nameErr; } ?>
              </p>
          </div>
          <!-- Email -->
          <div class="form-group">
              <label>Email:</label>
              <input type="text" class="form-control" placeholder="Enter email" name="email" value="<?php echo $set_email;?>">
              <p class="err-msg">
              <?php if($emailErr!=1){ echo $emailErr; } ?>
              </p>
          </div>

            <!-- phone number //-->
            <div class="form-group">
              <label>Phone:</label>
              <input type="text" class="form-control" placeholder="Enter PhoneNo" name="phone" value="<?php echo $set_phone;?>">
              <p class="err-msg">
              <?php if($phoneErr!=1){ echo $phoneErr; } ?>
              </p>
          </div>
          
          <!--subject-->
          <div class="form-group">
              <label>Subject:</label>
              <input type="text" class="form-control"  placeholder="Enter Subject" name="subject" value="<?php echo $set_subject;?>">
              <p class="err-msg">
              <?php if($subjectErr!=1){ echo $subjectErr; } ?>
              </p>
          </div>
          <!-- Message -->
          <div class="form-group">
            <label>Message</label>
            <textarea class="form-control" name="msg" placeholder="Enter Message"><?php echo $set_msg;?></textarea>
            <p class="err-msg">
              <?php if($msgErr!=1){ echo $msgErr; } ?>
              </p>
          </div>
          <input type="submit" class="btn btn-danger" value="Send Message" name="contact">
        </form>
      </div>
    </div>
    <div class="col-sm-4">
    </div>
  </div>
    
  </div>

  </body>
  </html>

