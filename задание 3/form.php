<form action="" method="POST">
  <input name="fio" />
  <input name="tel" />
  <input name="email" />
  <input type="radio" name="gender" value="male"> Мужской  
  <input type="radio" name="gender" value="female"> Женский<br>    
  <textarea name="bio"></textarea>    
  <input type="checkbox" name="contract" value="1"> С контрактом ознакомлен(а)<br>    
  <input type="submit" value="Сохранить" />
  <select name="year">
    <?php 
    for ($i = 1922; $i <= 2022; $i++) {
      printf('<option value="%d">%d год</option>', $i, $i);
    }
    ?>
  </select>
  
  <input type="submit" value="ok" />
</form>
