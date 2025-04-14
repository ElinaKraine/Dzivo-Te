function initMajasAsinhronieSkripti() {
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

    $.getJSON(
      "./assets/database/saglabatie_masivs.php?veids=Pirkt&tips=Maja",
      function (saglabatieSludinajumi) {
        $.ajax({
          url: `./assets/database/majas_list.php?${queryParams.toString()}`,
          type: "GET",
          success: function (response) {
            const majas = JSON.parse(response);
            let template = "";

            if (majas.length > 0) {
              majas.forEach((maja) => {
                const irSaglabats = saglabatieSludinajumi.includes(
                  parseInt(maja.id)
                );
                const sirdsKlase = irSaglabats ? "fa-solid" : "fa-regular";
                const sirdsKlase2 = irSaglabats ? "sirdsSarkans" : "";

                template += `
                <div class='sludinajums sludinajumsPardosanai' maja_id="${maja.id}">
                  <div class='attela-sirds'>
                    <img src="data:image/jpeg;base64,${maja.pirma_attela}" />
                    <a class='sirds saglabaSludinajumu ${sirdsKlase2}' data-id="${maja.id}">
                      <i class='${sirdsKlase} fa-heart'></i>
                    </a>
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
    );
  }

  $(document).on("click", ".mekleteFiltrusP", function (e) {
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

  $(document).on("click", "#izdest-filtrus-majas-pirkt", function (e) {
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

    tekosaMeklesana = "";
    tekosaKartosana = "datums_desc";

    fetchMajas("", {}, tekosaKartosana);
  });

  $(document).on("click", ".sludinajumsPardosanai", function () {
    let majaId = $(this).attr("maja_id");
    window.location.href = `maja_pirkt.php?id=${majaId}`;
  });

  $(document).on("click", ".saglabaSludinajumu", function (e) {
    e.stopPropagation();
    e.preventDefault();

    const poga = $(this);
    const sludinajumaId = poga.data("id");
    const irSaglabats = poga.find("i").hasClass("fa-solid");

    const url = irSaglabats
      ? "./assets/database/dzest_saglabatu.php"
      : "./assets/database/pievienot_saglabatiem.php";

    $.ajax({
      url: url,
      method: "POST",
      data: {
        id_sludinajums: sludinajumaId,
        veids: "Pirkt",
        tips: "Maja",
      },
      success: function (response) {
        if (response.success) {
          const ikona = poga.find("i");
          if (irSaglabats) {
            ikona.removeClass("fa-solid").addClass("fa-regular");
            poga.removeClass("sirdsSarkans");
          } else {
            ikona.removeClass("fa-regular").addClass("fa-solid");
            poga.addClass("sirdsSarkans");
          }
        } else {
          if (response.message === "unauthorized") {
            window.location.href = "./login.php";
          } else {
            alert(response.message || "Darbība neizdevās");
          }
        }
      },
      error: function () {
        alert("Neizdevās veikt darbību ar saglabātajiem.");
      },
    });
  });
}
