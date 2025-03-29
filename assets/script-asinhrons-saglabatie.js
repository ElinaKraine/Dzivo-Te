$(document).ready(function () {
  $.ajax({
    url: "./assets/database/saglabatie_list.php",
    method: "GET",
    success: function (response) {
      const saraksts = JSON.parse(response);
      let template = "";

      if (saraksts.length > 0) {
        saraksts.forEach((ieraksts) => {
          template += `
              <div class='sludinajums'>
                <div class='attela-sirds'>
                  <img src="data:image/jpeg;base64,${ieraksts.pirma_attela}" />
                </div>
                <p id='cena'>${ieraksts.cena} €</p>
                <div id='papildInfo'>
                  <p><i class='fa-solid fa-door-open'></i>${ieraksts.istabas}</p>
                  <p><i class='fa-solid fa-ruler-combined'></i> ${ieraksts.platiba} m<sup>2</sup></p>
                  <p><i class='fa-solid fa-stairs'></i> ${ieraksts.stavi}</p>
                </div>
                <p id='adrese'>${ieraksts.pilseta}, ${ieraksts.iela} ${ieraksts.majas_numurs}</p>
              </div>
            `;
        });
      } else {
        template =
          "<p class='navRezultatus'>Jums nav saglabātu sludinājumu.</p>";
      }

      $("#saglabatie").html(template);
    },
    error: function () {
      alert("Neizdevās ielādēt saglabātos sludinājumus.");
    },
  });
});
