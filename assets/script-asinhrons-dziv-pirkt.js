function initDzivokliAsinhronieSkripti() {
  let tekosaMeklesana = "";
  let tekosaKartosana = "datums_desc";

  fetchDzivokli();

  function fetchDzivokli(meklet = "", filtri = {}, sortBy = "datums_desc") {
    let queryParams = new URLSearchParams({ meklet, sort: sortBy });

    for (let key in filtri) {
      if (filtri[key]) {
        queryParams.append(key, filtri[key]);
      }
    }

    $.getJSON(
      "./assets/database/saglabatie_masivs.php?veids=Pirkt",
      function (saglabatieSludinajumi) {
        $.ajax({
          url: `./assets/database/dzivokli_list.php?${queryParams.toString()}`,
          type: "GET",
          success: function (response) {
            const dzivokli = JSON.parse(response);
            let template = "";

            if (dzivokli.length > 0) {
              dzivokli.forEach((dzivoklis) => {
                const irSaglabats = saglabatieSludinajumi.includes(
                  parseInt(dzivoklis.id)
                );
                const sirdsKlase = irSaglabats ? "fa-solid" : "fa-regular";
                const sirdsKlase2 = irSaglabats ? "sirdsSarkans" : "";

                template += `
                  <div class='sludinajums sludinajumsPardosanaiDziv' dzivoklis_id="${dzivoklis.id}">
                    <div class='attela-sirds'>
                      <img src="data:image/jpeg;base64,${dzivoklis.pirma_attela}" />
                      <a class='sirds saglabaSludinajumu ${sirdsKlase2}' data-id="${dzivoklis.id}">
                        <i class='${sirdsKlase} fa-heart'></i>
                      </a>
                    </div>
                    <p id='cena'>${dzivoklis.cena} €</p>
                    <div id='papildInfo'>
                      <p><i class='fa-solid fa-door-open'></i>${dzivoklis.istabas}</p>
                      <p><i class='fa-solid fa-ruler-combined'></i> ${dzivoklis.platiba} m<sup>2</sup></p>
                      <p><i class='fa-solid fa-stairs'></i> ${dzivoklis.stavs}</p>
                    </div>
                    <p id='adrese'>${dzivoklis.pilseta}, ${dzivoklis.iela} ${dzivoklis.majas_numurs}/${dzivoklis.dzivokla_numurs}</p>
                  </div>
                `;
              });
            } else {
              template = `<p class='navRezultatus'>Nav rezultātu atbilstošu meklēšanai</p>`;
            }

            $("#dzivokli").html(template);
          },
          error: function () {
            alert("Neizdevas ieladet datus");
          },
        });
      }
    );
  }

  $(document).on("click", ".mekleteFiltrusPD", function (e) {
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

    fetchDzivokli(tekosaMeklesana, filtri, tekosaKartosana);
  });

  $(document).on("click", "#izdest-filtrus-dzivokli-pirkt", function (e) {
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

    fetchDzivokli("", {}, tekosaKartosana);
  });

  $(document).on("change", ".kartosanaPD select", function () {
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

    fetchDzivokli(tekosaMeklesana, filtri, tekosaKartosana);
  });

  $(document).on("click", ".sludinajumsPardosanaiDziv", function () {
    let dzivoklisId = $(this).attr("dzivoklis_id");
    window.location.href = `dzivoklis_pirkt.php?id=${dzivoklisId}`;
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
