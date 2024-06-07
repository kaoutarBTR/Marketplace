<?php
// $input = 'BTRKaoutar@2003';
// $test = password_hash($input,PASSWORD_BCRYPT);
// $motDePasseHache = hash('sha512',  $input);
// echo $test;
// $hashedPassword = password_hash('1234567', PASSWORD_BCRYPT);


if (password_verify('BTRKaoutar@2003', '$2y$10$9HR/stdVywx6C4n4zBA65.CDF/fmV2BalzlDQxn4YGH6pR.KCEoJG')) {
  echo 'Connexion réussie';
} else {
  echo  'Mot de passe incorrect';
}


?>