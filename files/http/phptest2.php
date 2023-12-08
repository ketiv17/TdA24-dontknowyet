<html lang = "cs">
   <body>
      <?php
         $servername = "resurrectiongc.live";
         $username = "api";
         $password = "Ahoj-Jaksemas5";
         $dbname = "api";
         $conn = new mysqli($servername, $username, $password, $dbname); //Odkaz (Vytvoreni spojeni)
         $sql = "SELECT * FROM users"; //Odkaz (Vytvoreni dotazu v jazyce MySQL)
         $result = mysqli_query($conn, $sql); //Odkaz (Odeslani a zpracovani dotazu serverem)
         while($row = $result->fetch_assoc()) {
            echo "id: " . $row["uuid"]. " - Jmeno: " . $row["first_name"]. " - PermLVL: " . $row["last_name"]. " - Creation date: " . $row["emails"]."<br>"; }
         $conn->close(); //Odkaz (Uzavreni spojeni)

      ?>
   </body>
</html>