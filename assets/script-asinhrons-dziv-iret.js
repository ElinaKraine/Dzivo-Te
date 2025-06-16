$(document).ready(function () {
  let tekosaMeklesana = "";
  let tekosaKartosana = "datums_desc";
  let ievaditiDatumi = false;

  function galigaCena(no, lidz, cena_diena, cena_nedela, cena_menesis) {
    const dienasSkaits = dienasSkaitsFunkcija(no, lidz);
    let kopa = 0;

    if (dienasSkaits < 7) {
      kopa = dienasSkaits * cena_diena;
    } else if (dienasSkaits < 32) {
      const nedelas = Math.floor(dienasSkaits / 7);
      const atlikusieDienas = dienasSkaits % 7;
      kopa = nedelas * cena_nedela + atlikusieDienas * cena_diena;
    } else {
      const menesi = Math.floor(dienasSkaits / 30);
      const atlikusieDienas = dienasSkaits % 30;
      kopa = menesi * cena_menesis + atlikusieDienas * cena_diena;
    }

    return kopa.toFixed(2);
  }

  function dienasSkaitsFunkcija(no, lidz) {
    const datumsNo = new Date(no);
    const datumsLidz = new Date(lidz);
    return Math.ceil((datumsLidz - datumsNo) / (1000 * 60 * 60 * 24));
  }

  function getDienasPareizaForma(skaits) {
    if (skaits === 1 || (skaits % 10 === 1 && skaits % 100 !== 11)) {
      return "dienu";
    }
    return "dienām";
  }

  function paradit_pazinojumu(pazinojums) {
    const modalHTML = `
      <div class="modal modal-active" id="modal-message">
        <div class="modal-box">
          <div class="close-modal" data-target="#modal-message"><i class="fas fa-times"></i></div>
          <h2>${pazinojums}</h2>
        </div>
      </div>
    `;
    $("body").append(modalHTML);
  }

  function fetchIresanasDzivokli(
    meklet = "",
    filtri = {},
    sortBy = "datums_desc",
    no = "",
    lidz = ""
  ) {
    let queryParams = new URLSearchParams({
      meklet,
      sort: sortBy,
      noDziv: no,
      lidzDziv: lidz,
    });

    for (let key in filtri) {
      if (filtri[key]) {
        queryParams.append(key, filtri[key]);
      }
    }

    $.getJSON(
      "./assets/database/saglabatie_masivs.php?veids=Iret&tips=Dzivoklis",
      function (saglabatieSludinajumi) {
        $.ajax({
          url: `./assets/database/pieejami_dzivokli_list.php?${queryParams.toString()}`,
          type: "GET",
          success: function (response) {
            const dzivokli = JSON.parse(response);
            let template = "";

            const minCena = parseFloat(filtri.minCena) || 0;
            const maxCena = parseFloat(filtri.maxCena) || Infinity;

            if (dzivokli.length > 0) {
              dzivokli.forEach((dzivoklis) => {
                const galaCena = galigaCena(
                  no,
                  lidz,
                  parseFloat(dzivoklis.cena_diena),
                  parseFloat(dzivoklis.cena_nedela),
                  parseFloat(dzivoklis.cena_menesis)
                );

                if (galaCena < minCena || galaCena > maxCena) return;

                const parDienas = dienasSkaitsFunkcija(no, lidz);

                const irSaglabats = saglabatieSludinajumi.includes(
                  parseInt(dzivoklis.id)
                );
                const sirdsKlase = irSaglabats ? "fa-solid" : "fa-regular";
                const sirdsKlase2 = irSaglabats ? "sirdsSarkans" : "";

                template += `
                    <div class='sludinajums sludinajumsIresanai' 
                      dzivoklis_id="${dzivoklis.id}"
                      data-cena_diena="${dzivoklis.cena_diena}" 
                      data-cena_nedela="${dzivoklis.cena_nedela}" 
                      data-cena_menesis="${dzivoklis.cena_menesis}"
                    >
                      <div class='attela-sirds'>
                        <img src="data:image/jpeg;base64,${
                          dzivoklis.pirma_attela
                        }" />
                        <a class='sirds saglabatSludinajumuDziv ${sirdsKlase2}' data-id="${
                  dzivoklis.id
                }">
                          <i class='${sirdsKlase} fa-heart'></i>
                        </a>
                      </div>
                      <p id='cena'>${galaCena} € par ${parDienas} ${getDienasPareizaForma(
                  parDienas
                )}</p>
                      <div id='papildInfo'>
                        <p><i class='fa-solid fa-door-open'></i>${
                          dzivoklis.istabas
                        }</p>
                        <p><i class='fa-solid fa-ruler-combined'></i> ${
                          dzivoklis.platiba
                        } m<sup>2</sup></p>
                        <p><i class='fa-solid fa-stairs'></i> ${
                          dzivoklis.stavi
                        }</p>
                      </div>
                      <p id='adrese'>${dzivoklis.pilseta}, ${dzivoklis.iela} ${
                  dzivoklis.majas_numurs
                }/${dzivoklis.dzivokla_numurs}</p>
                    </div>
                  `;
              });

              $(".dzivokliSaturs.iresanasBack").removeClass("iresanasBack");
              if (template == "") {
                template =
                  "<p class='navRezultatus'>Nav rezultātu atbilstošu meklēšanai</p>";
              }
            } else {
              template =
                "<p class='navRezultatus'>Nav rezultātu atbilstošu meklēšanai</p>";
            }
            console.log(template);
            $("#dzivokliIret").html(template);
          },
          error: function () {
            alert("Kļūda ielādējot pieejamās mājas.");
          },
        });
      }
    );
  }

  $(document).on("submit", ".iresanasDatumiDziv", function (e) {
    e.preventDefault();

    const no = $("input[name='noDziv']").val();
    const lidz = $("input[name='lidzDziv']").val();

    if (!no || !lidz) {
      alert("Lūdzu, ievadiet abus datumus!");
      return;
    }

    const noDate = new Date(no);
    const lidzDate = new Date(lidz);

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);

    if (noDate < tomorrow || lidzDate < tomorrow) {
      alert("Jūs nevarat izvēlēties pagājušās vai šodienas dienas!");
      return;
    }

    const timeDiff = lidzDate.getTime() - noDate.getTime();
    const dayDiff = timeDiff / (1000 * 3600 * 24);

    if (dayDiff < 1) {
      paradit_pazinojumu("Datumu atšķirībai jābūt vismaz 1 dienai.");
      return;
    }

    ievaditiDatumi = true;
    tekosaMeklesana = $("#meklet-lauks").val();
    fetchIresanasDzivokli(tekosaMeklesana, {}, tekosaKartosana, no, lidz);
  });

  $(document).on("click", ".mekleteFiltrusID", function (e) {
    e.preventDefault();

    if (!ievaditiDatumi) {
      alert("Vispirms ievadiet datumus!");
      return;
    }

    const no = $("input[name='noDziv']").val();
    const lidz = $("input[name='lidzDziv']").val();
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

    fetchIresanasDzivokli(tekosaMeklesana, filtri, tekosaKartosana, no, lidz);
  });

  $(document).on("change", "#kartosanasOpcijasID", function () {
    if (!ievaditiDatumi) {
      alert("Vispirms ievadiet datumus!");
      return;
    }

    tekosaKartosana = $(this).val();
    const no = $("input[name='noDziv']").val();
    const lidz = $("input[name='lidzDziv']").val();

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

    fetchIresanasDzivokli(tekosaMeklesana, filtri, tekosaKartosana, no, lidz);
  });

  $(document).on("click", "#izdest-filtrus-dzivokli-iret", function (e) {
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

    if (!ievaditiDatumi) {
      alert("Vispirms ievadiet datumus!");
      return;
    }

    const no = $("input[name='noDziv']").val();
    const lidz = $("input[name='lidzDziv']").val();

    fetchIresanasDzivokli("", {}, tekosaKartosana, no, lidz);
  });

  function izveidotUnNosutitFormu(dzivoklisId, no, lidz, galaCena) {
    const forma = document.createElement("form");
    forma.method = "POST";
    forma.action = "dzivoklis_iret.php";

    const lauki = { id: dzivoklisId, no: no, lidz: lidz, total: galaCena };

    for (let key in lauki) {
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = key;
      input.value = lauki[key];
      forma.appendChild(input);
    }

    document.body.appendChild(forma);
    forma.submit();
  }

  $(document).on("click", ".sludinajumsIresanai", function () {
    let dzivoklisId = $(this).attr("dzivoklis_id");
    const no = $("input[name='noDziv']").val();
    const lidz = $("input[name='lidzDziv']").val();

    const cena_diena = parseFloat($(this).attr("data-cena_diena"));
    const cena_nedela = parseFloat($(this).attr("data-cena_nedela"));
    const cena_menesis = parseFloat($(this).attr("data-cena_menesis"));

    const galaCena = galigaCena(
      no,
      lidz,
      cena_diena,
      cena_nedela,
      cena_menesis
    );
    izveidotUnNosutitFormu(dzivoklisId, no, lidz, galaCena);
  });

  $(document).on("click", ".saglabatSludinajumuDziv", function (e) {
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
      dataType: "json",
      data: {
        id_sludinajums: sludinajumaId,
        veids: "Iret",
        tips: "Dzivoklis",
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
});
