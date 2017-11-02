<?PHP

  $salted_pw="76825849d9f96cb454b57fe64e5b5d89ccad030e";

  $user='Denngarr B\'tarn';
  $pw='$2y$10$EYMgK2ZEAqbcGeTF.T0fmuiVFpFKvovu0DwIRTg0PLAHjIrRPLgQe';
  $salt="TefkUw";

  $pre = sha1($pw);
  $post = sha1($pw . $salt);

echo $pre."\n";
echo $post."\n";
echo $salted_pw."\n";

?>
