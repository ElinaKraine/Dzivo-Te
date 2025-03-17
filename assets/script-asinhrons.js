$(document).ready(function () {
  let tekosaMeklesana = "";
  let tekosaKartosana = "datums_desc";

  fetchMajas();

  function fetchMajas(meklet = "", filtri = {}, sortBy = "datums_desc") {
    let queryParams = new URLSearchParams({ meklet, sort: sortBy });

    for (let key in filtri) {
      if (filtri[key]) {
        queryParams.append(key, filtri[key]);
      }
    }

    $.ajax({
      url: `./admin/database/majas_list.php?${queryParams.toString()}`,
      type: "GET",
      success: function (response) {
        const majas = JSON.parse(response);
        let template = "";

        if (majas.length > 0) {
          majas.forEach((maja) => {
            template += `
              <div class='sludinajums' maja_id="${maja.id}">
                <div class='attela-sirds'>
                  <img src="data:image/jpeg;base64,${maja.pirma_attela}" />
                  <a class='sirds'><i class='fa-regular fa-heart'></i></a>
                </div>
                <p id='cena'>${maja.cena} €</p>
                <div id='papildInfo'>
                  <p><i class='fa-solid fa-door-open'></i>${maja.istabas}</p>
                  <p><i class='fa-solid fa-ruler-combined'></i> ${maja.platiba} m<sup>2</sup></p>
                  <p><i class='fa-solid fa-stairs'></i> ${maja.stavi}</p>
                </div>
                <p id='adrese'>${maja.pilseta}, ${maja.iela} ${maja.majas_numurs}</p>
              </div>
            `;
          });
        } else {
          template = `<p class='navRezultatus'>Nav rezultātu atbilstošu meklēšanai</p>`;
        }
        $("#majas").html(template);
      },
      error: function () {
        alert("Neizdevas ieladet datus");
      },
    });
  }

  $(document).on("click", ".mekleteFiltrus", function (e) {
    e.preventDefault();
    tekosaMeklesana = $("#meklet-lauks").val();

    let filtri = {
      minCena: $("input[name='minimalaCena']").val(),
      maxCena: $("input[name='maksimalaCena']").val(),
      minIstabas: $("input[name='minimumIstabas']").val(),
      maxIstabas: $("input[name='maksimumIstabas']").val(),
      minPlatiba: $("input[name='minimalaPlatiba']").val(),
      maxPlatiba: $("input[name='maksimalaPlatiba']").val(),
      minStavi: $("input[name='minimumStavus']").val(),
      maxStavi: $("input[name='maksimumStavus']").val(),
    };

    fetchMajas(tekosaMeklesana, filtri, tekosaKartosana);
  });

  $(document).on("change", ".kartosana select", function () {
    tekosaKartosana = $(this).val();

    let filtri = {
      minCena: $("input[name='minimalaCena']").val(),
      maxCena: $("input[name='maksimalaCena']").val(),
      minIstabas: $("input[name='minimumIstabas']").val(),
      maxIstabas: $("input[name='maksimumIstabas']").val(),
      minPlatiba: $("input[name='minimalaPlatiba']").val(),
      maxPlatiba: $("input[name='maksimalaPlatiba']").val(),
      minStavi: $("input[name='minimumStavus']").val(),
      maxStavi: $("input[name='maksimumStavus']").val(),
    };

    fetchMajas(tekosaMeklesana, filtri, tekosaKartosana);
  });

  $(document).on("click", "#izdest-filtrus", function (e) {
    e.preventDefault();

    $("#meklet-lauks").val("");
    $("input[name='minimalaCena']").val("");
    $("input[name='maksimalaCena']").val("");
    $("input[name='minimumIstabas']").val("");
    $("input[name='maksimumIstabas']").val("");
    $("input[name='minimalaPlatiba']").val("");
    $("input[name='maksimalaPlatiba']").val("");
    $("input[name='minimumStavus']").val("");
    $("input[name='maksimumStavus']").val("");

    fetchMajas("", {}, tekosaKartosana);
  });

  $(document).on("click", ".sludinajums", function () {
    let majaId = $(this).attr("maja_id");
    window.location.href = `maja.php?id=${majaId}`;
  });
});
