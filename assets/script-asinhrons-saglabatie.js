$(document).ready(function () {
  let saglabasanaNotiek = false;

  $.ajax({
    url: "./assets/database/saglabatie_list.php",
    method: "GET",
    dataType: "json",
    success: function (saraksts) {
      let template = "";

      if (saraksts.length > 0) {
        saraksts.forEach((ieraksts) => {
          let cenaBloks = "";
          if (ieraksts.veids === "Pirkt") {
            cenaBloks = `<p id='cena'>${ieraksts.cena} €</p>`;
          } else if (ieraksts.veids === "Iret") {
            cenaBloks = `
              <p id='cena'>
                ${ieraksts.cena_diena} €/dienā<br>
                ${ieraksts.cena_nedela} €/ned.<br>
                ${ieraksts.cena_menesis} €/mēn.
              </p>
            `;
          }

          template += `
            <div class='sludinajums' data-id="${ieraksts.id}" data-veids="${ieraksts.veids}">
              <div class='attela-sirds'>
                <img src="data:image/jpeg;base64,${ieraksts.pirma_attela}" />
                <a class='sirds saglabataSludinajumu sirdsSarkans' data-id="${ieraksts.id}" data-veids="${ieraksts.veids}">
                  <i class='fa-solid fa-heart'></i>
                </a>
              </div>
              ${cenaBloks}
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

  $(document).on("click", ".sludinajums", function () {
    const id = $(this).data("id");
    const veids = $(this).data("veids");

    if (!id || !veids) return;

    if (veids === "Pirkt") {
      window.location.href = `maja_pirkt.php?id=${id}`;
    } else if (veids === "Iret") {
      window.location.href = `maja_iret_saglabats.php?id=${id}`;
    }
  });

  $(document).on("click", ".saglabataSludinajumu", function (e) {
    e.preventDefault();
    e.stopPropagation();

    if (saglabasanaNotiek) return;
    saglabasanaNotiek = true;

    const poga = $(this);
    const id = poga.data("id");
    const veids = poga.data("veids");

    $.ajax({
      url: "./assets/database/dzest_saglabatu.php",
      method: "POST",
      dataType: "json",
      data: {
        id_sludinajums: id,
        veids: veids,
      },
      success: function (response) {
        if (response.success) {
          poga.closest(".sludinajums").remove();

          if ($(".sludinajums").length === 0) {
            $("#saglabatie").html(
              "<p class='navRezultatus'>Jums nav saglabātu sludinājumu.</p>"
            );
          }
        } else {
          if (response.message === "unauthorized") {
            window.location.href = "./login.php";
          } else {
            alert(response.message || "Kļūda dzēšot no saglabātajiem.");
          }
        }
      },
      error: function () {
        alert("Neizdevās dzēst no saglabātajiem.");
      },
      complete: function () {
        saglabasanaNotiek = false;
      },
    });
  });
});
