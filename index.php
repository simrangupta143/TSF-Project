<?php
// Connecting to the Database
$servername = "localhost";
$username = "root";
$password = "";
$database="customres";

// Create a connection
$conn = mysqli_connect($servername, $username, $password,$database);

// Die if connection was not successful
if (!$conn){
    die("Sorry we failed to connect: ". mysqli_connect_error());
}
else{
    //echo "Connection was successful";
}

?>





<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="fontawesome\css\all.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="home.css?v=<?php  echo time(); ?>">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banking System</title>
</head>

<body>
    <div class="container">
        <div class="details" id="item1">
           <div class="logo">
               <img src="logo1.png" alt="">
               <h1>The Sparks Foundation</h1>
            </div>
           <div class="display">
               <h2>Online Banking</h2>
                <ul class="list">
                    <li>
                        <p> <i class="fas fa-check"></i>
                            <span>Pay Your Bills Online</span>
                        </p>
                    </li>
                    <li>
                        <p> <i class="fas fa-check"></i>
                            <span> View Your Transactions</span>
                        </p>
                    </li>
                    <li>
                        <p> <i class="fas fa-check"></i>
                            <span>Access your account 24/7</span>
                        </p>
                    </li>
                </ul>
           </div>
        <div id="buttons">
            <button class="btn" onclick="showtransactions()">Transaction</button>
            <button class="btn" onclick="showcustomers()">Customers</button>
        </div>
        </div>
            <div id="rightside">
                <div class="navigation">
                    <nav class="navbar">
                        <ul class="navlist">
                            <li><button onclick="goHome()" title="Go Back to HomePage"> Home </button> </li>
                            <li><button> Blog </button></li>
                            <li><button> Services </button></li>
                            <li><button> Contact Us </button></li>
                        </ul>
                    </nav>
                </div>
                <div id="image">
                    <img src="pt.png" alt="">
                </div>

<!-- Transfer form  -->
<div id="card">
    <div id="back">
        <i class="fas fa-arrow-left" onclick="goback()" id="bk"></i>
     </div>
     <div class="heading">
         <h3>Transfer Money</h3>
     </div>


    <form action="/sparkproject/index.php" method="post">
        <input type="text" id="sender" name="sender" placeholder="Your Name*" required>
        <input type="text" id="sid" name="sid" placeholder="Your Id*" required>
        <input type="text" id="receiver" name="receiver" placeholder="Receiver' Name*" required>
        <input type="text" id="rid" name="rid" placeholder="Receiver's Id*" required>
        <input type="text" id="amount" name="amount" placeholder="Amount*" required>
        <button type="submit">Transfer</button>
    </form>
</div>
<?php
        $sum=0;
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $sender=$_POST['sender'];
        $sid=$_POST['sid'];
        $receiver=$_POST['receiver'];
        $rid=$_POST['rid'];
        $sum=$_POST['amount'];

        $id=rand(1000,10000);
        //  Updating Sender's Balance
        $getRName="SELECT * FROM customertable WHERE CustomerID='$sid'";
        $Senders=mysqli_fetch_array(mysqli_query($conn,$getRName));
        if( (int)$Senders['CurrentBalance'] > $_POST['amount']  and (int)$Senders['CurrentBalance']>0 )
        {
            $sent= (int)$Senders['CurrentBalance'] - $_POST['amount'];
            $updatequeryS= "UPDATE `customertable` SET `CurrentBalance` = '$sent' WHERE `customertable`.`CustomerId` = '$sid' ";
            $r1 = mysqli_query($conn, $updatequeryS);
            
            
// Inserting into Transaction table
            $sqlquery="INSERT INTO `transaction` (`Sno`, `id`, `sender`, `SenderId`, `receiver`, `ReceiverId`, `amount`, `date`) VALUES (NULL, '$id', '$sender', '$sid', '$receiver', '$rid', '$sum', current_timestamp())";
            
            $result = mysqli_query($conn, $sqlquery);

// updating Receuver's Balance
            $getRName="SELECT * FROM customertable WHERE CustomerID='$rid'";
            $Reciever=mysqli_fetch_array(mysqli_query($conn,$getRName));
            $RBal=$Reciever['CurrentBalance'];
            $Recieve= (int)$RBal + $_POST['amount'];
        $updatequeryR= "UPDATE `customertable` SET `CurrentBalance` = '$Recieve' WHERE `customertable`.`CustomerId` = '$rid' ";
        $r2= mysqli_query($conn, $updatequeryR);
        }

        else{
            echo 'alert("Insufficient Funds!");';
        }
    }
?>

                <table id="containertable">
                    <thead>
                        <tr>
                            <th>Sno</th>
                            <th>Customer Id</th>
                            <th>Customer Name</th>
                            <th>Customer Contact</th>
                            <th>Customer Email</th>
                            <th>Current Balance</th>
                        </tr>
                    </thead>

                    <tbody id="tablebody">
       <?php
                         $selectquery= 'SELECT * FROM `customertable`';
                         $query = mysqli_query($conn,$selectquery);
         
                         while($res = mysqli_fetch_array($query)){
                             
                 ?>
           <tr onclick="showtransfer()">
               <td> <?php echo $res['Sno'] ?> </td>
               <td><?php echo $res['CustomerId'] ?></td>
               <td><?php echo $res['CustomerName'] ?></td>
               <td><?php echo $res['ContactNumber'] ?></td>
               <td><?php echo $res['CustomerEmail'] ?></td>
               <td><?php echo $res['CurrentBalance'] ?></td>
           </tr>
           <?php
                         }
            ?>

       </tbody>
                </table>




<!-- Transfer Table -->

            <table id="transfertable">
                <thead>
                    <tr>
                        <th>Sno</th>
                        <th>Transaction Id</th>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th>Amount</th>
                    </tr>
                    
                </thead>

                <tbody id="tablebody">
                <?php
                         $selectquery= 'SELECT * FROM `transaction`';
                         $query = mysqli_query($conn,$selectquery);
         
                         while($res = mysqli_fetch_array($query)){
                             
                 ?>
                    <tr>
                        <td> <?php echo $res['Sno'];    ?></td>
                        <td><?php echo $res['id'];    ?></td>
                        <td><?php echo $res['sender'];    ?> </td>
                        <td><?php echo $res['receiver'];    ?> </td>
                        <td><?php echo $res['amount'];    ?> </td>
                        
                    </tr> 
                    <?php
                         }
                         ?> 
                </tbody>
            </table>
        </div>
    </div>
        <footer>
            <p>Copyright &copy; The Sparks Foundation Banking</p>
            <p>Designed and Developed by: Simran Raj Gupta </p>
            <div class="icon">
                <a href="mailto:be15121.18@bitmesra.ac.in?subject=Mail From TSF Banking Site&body=Hello Developer," target="_blank" title="Drop me an Email"><i class="fas fa-envelope"></i></a>
                <a href="https://www.linkedin.com/in/simrangupta30240408/" target="_blank" title="Get me on LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </footer>
<script type="text/javascript">
        function showcustomers(){
            document.getElementById("transfertable").style.display="none";
            document.getElementById("containertable").style.display="table";
            document.getElementById("image").style.display="none";
            document.getElementById("card").style.display="none";
        }
        function goback(){
            document.getElementById("card").style.display="none";
            document.getElementById("transfertable").style.display="none";
            document.getElementById("containertable").style.display="table";
            document.getElementById("image").style.display="none";
        }
        function showtransactions(){
            document.getElementById("card").style.display="none";
            document.getElementById("transfertable").style.display="table";
            document.getElementById("containertable").style.display="none";
            document.getElementById("image").style.display="none";
        }
        function goHome(){
            document.getElementById("containertable").style.display="none";
            document.getElementById("transfertable").style.display="none";
            document.getElementById("image").style.display="block";
            document.getElementById("card").style.display="none";

        }
        function showtransfer(){
            document.getElementById("card").style.display="block";
            document.getElementById("containertable").style.display="none";
            document.getElementById("transfertable").style.display="none";
            document.getElementById("image").style.display="none";

        }
</script>   

</body>
</html>