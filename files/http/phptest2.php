<html lang = "cs">
   <body>
      <?php
         $servername = "resurrectiongc.live";
         $username = "api";
         $password = "Ahoj-Jaksemas5";
         $dbname = "api";
         $conn = new mysqli($servername, $username, $password, $dbname); //Odkaz (Vytvoreni spojeni)
         $sql = "SELECT name, id, permlvl, created FROM users"; //Odkaz (Vytvoreni dotazu v jazyce MySQL)
         $result = mysqli_query($conn, $sql); //Odkaz (Odeslani a zpracovani dotazu serverem)
         while($row = $result->fetch_assoc()) {
            echo "id: " . $row["id"]. " - Jmeno: " . $row["name"]. " - PermLVL: " . $row["permlvl"]. " - Creation date: " . $row["created"]."<br>"; }
         $conn->close(); //Odkaz (Uzavreni spojeni)

      ?>
   </body>
</html>