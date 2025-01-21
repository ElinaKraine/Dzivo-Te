<!DOCTYPE html>
<html lang="lv">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Māju Vieta - Reģistrēšana</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body id="registracijaBody">
    <div class="registracijasKaste">
        <div class="majuVieta">
            <img src="images/logo.png">
            <h2>Māju Vieta</h2>
        </div>
        <h2>Reģistrēšana</h2>
        <form>
            <div class="vardsUzvards">
                <input type="text" name="vards" placeholder="Vārds *" required>
                <input type="text" name="uzvards" placeholder="Uzvārds *" required>
            </div>
            <input type="email" name="epastaAdrese" placeholder="E-pasta adrese *" required>
            <input type="text" name="talrunis" placeholder="Tālrunis *" required>
            <input type="password" name="paroleR" placeholder="Parole *" required>
            <input type="password" name="paroleAtkartoti" placeholder="Parole (atkārtoti) *" required>
            <button type="submit" class="btn">Reģistrēties</button>
        </form>
    </div>
</body>
</html>