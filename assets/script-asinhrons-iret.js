$(document).ready(function () {
  // Mainīgie pašreizējai meklēšanai un kārtošanai
  let tekosaMeklesana = "";
  let tekosaKartosana = "datums_desc";
  let ievaditiDatumi = false;

  // Kopējās īres cenas aprēķināšana atkarībā no īres ilguma un cenām
  function galigaCena(no, lidz, cena_diena, cena_nedela, cena_menesis) {
    const dienasSkaits = dienasSkaitsFunkcija(no, lidz);
    let kopa = 0;

    if (dienasSkaits < 7) {
      // Mazāk nekā nedēļu - rēķināties ar dienas cenu
      kopa = dienasSkaits * cena_diena;
    } else if (dienasSkaits < 32) {
      // Mazāk par mēnesi - skaits pa nedēļām + atlikums pa dienām
      const nedelas = Math.floor(dienasSkaits / 7);
      const atlikusieDienas = dienasSkaits % 7;
      kopa = nedelas * cena_nedela + atlikusieDienas * cena_diena;
    } else {
      // Vairāk nekā mēnesi - skaits pa mēnešiem + atlikums pa dienām
      const menesi = Math.floor(dienasSkaits / 30);
      const atlikusieDienas = dienasSkaits % 30;
      kopa = menesi * cena_menesis + atlikusieDienas * cena_diena;
    }

    return kopa.toFixed(2);
  }

  // Aprēķina dienu skaitu starp diviem datumiem
  function dienasSkaitsFunkcija(no, lidz) {
    const datumsNo = new Date(no);
    const datumsLidz = new Date(lidz);
    return Math.ceil((datumsLidz - datumsNo) / (1000 * 60 * 60 * 24));
  }

  // Atgrieziet pareizo galotni vārdam “diena”
  function getDienasPareizaForma(skaits) {
    if (skaits === 1 || (skaits % 10 === 1 && skaits % 100 !== 11)) {
      return "dienu";
    }
    return "dienām";
  }

  // Pieejamo māju asinhronā ielāde un filtrēšana
  function fetchIresanasMajas(
    meklet = "",
    filtri = {},
    sortBy = "datums_desc",
    no = "",
    lidz = ""
  ) {
    let queryParams = new URLSearchParams({ meklet, sort: sortBy, no, lidz });

    for (let key in filtri) {
      if (filtri[key]) {
        queryParams.append(key, filtri[key]);
      }
    }

    // Iegūt saglabāto sludinājumu sarakstu
    $.getJSON(
      "./assets/database/saglabatie_masivs.php?veids=Iret&tips=Maja",
      function (saglabatieSludinajumi) {
        // Iegūt pieejamo māju sarakstu
        $.ajax({
          url: `./assets/database/pieejamie_majas_list.php?${queryParams.toString()}`,
          type: "GET",
          success: function (response) {
            const majas = JSON.parse(response);
            let template = "";
            const minCena = parseFloat(filtri.minCena) || 0;
            const maxCena = parseFloat(filtri.maxCena) || Infinity;

            if (majas.length > 0) {
              majas.forEach((maja) => {
                const galaCena = galigaCena(
                  no,
                  lidz,
                  parseFloat(maja.cena_diena),
                  parseFloat(maja.cena_nedela),
                  parseFloat(maja.cena_menesis)
                );

                // Pārbaude, vai cena iekļaujas norādītajā diapazonā
                if (galaCena < minCena || galaCena > maxCena) return;

                const parDienas = dienasSkaitsFunkcija(no, lidz);

                // Pārbaude, vai sludinājums ir saglabāts
                const irSaglabats = saglabatieSludinajumi.includes(
                  parseInt(maja.id)
                );
                const sirdsKlase = irSaglabats ? "fa-solid" : "fa-regular";
                const sirdsKlase2 = irSaglabats ? "sirdsSarkans" : "";

                // HTML sludinājuma šablons
                template += `
                  <div class='sludinajums sludinajumsIresanai' 
                    maja_id="${maja.id}"
                    data-cena_diena="${maja.cena_diena}" 
                    data-cena_nedela="${maja.cena_nedela}" 
                    data-cena_menesis="${maja.cena_menesis}"
                  >
                    <div class='attela-sirds'>
                      <img src="data:image/jpeg;base64,${maja.pirma_attela}" />
                      <a class='sirds saglabatSludinajumu ${sirdsKlase2}' data-id="${
                  maja.id
                }">
                        <i class='${sirdsKlase} fa-heart'></i>
                      </a>
                    </div>
                    <p id='cena'>${galaCena} € par ${parDienas} ${getDienasPareizaForma(
                  parDienas
                )}</p>
                    <div id='papildInfo'>
                      <p><i class='fa-solid fa-door-open'></i>${
                        maja.istabas
                      }</p>
                      <p><i class='fa-solid fa-ruler-combined'></i> ${
                        maja.platiba
                      } m<sup>2</sup></p>
                      <p><i class='fa-solid fa-stairs'></i> ${maja.stavi}</p>
                    </div>
                    <p id='adrese'>${maja.pilseta}, ${maja.iela} ${
                  maja.majas_numurs
                }</p>
                  </div>
                `;
              });

              $(".majasSaturs.iresanasBack").removeClass("iresanasBack");
            } else {
              template =
                "<p class='navRezultatus'>Izvēlētajā periodā nav brīvu mājokļu.</p>";
            }

            $("#majasIret").html(template);
          },
          error: function () {
            alert("Kļūda ielādējot pieejamās mājas.");
          },
        });
      }
    );
  }

  // Veidlapas ar atlasītiem īres datumiem nosūtīšana
  $(document).on("submit", ".iresanasDatumi", function (e) {
    e.preventDefault();

    const no = $("input[name='no']").val();
    const lidz = $("input[name='lidz']").val();

    if (!no || !lidz) {
      alert("Lūdzu, ievadiet abus datumus!");
      return;
    }

    const noDate = new Date(no);
    const lidzDate = new Date(lidz);

    const sodien = new Date();
    sodien.setHours(0, 0, 0, 0);

    const rit = new Date(sodien);
    rit.setDate(sodien.getDate() + 1);

    // Pārbaude, vai datums nav pagātnē vai šodien.
    if (noDate < rit || lidzDate < rit) {
      alert("Jūs nevarat izvēlēties pagājušās vai šodienas dienas!");
      return;
    }

    const timeDiff = lidzDate.getTime() - noDate.getTime();
    const dayDiff = timeDiff / (1000 * 3600 * 24);

    if (dayDiff < 1) {
      alert("Datumu atšķirībai jābūt vismaz 1 dienai.");
      return;
    }

    ievaditiDatumi = true;
    tekosaMeklesana = $("#meklet-lauks").val();
    fetchIresanasMajas(tekosaMeklesana, {}, tekosaKartosana, no, lidz);
  });

  // Meklēt ar filtriem apstrāde
  $(document).on("click", ".mekleteFiltrusI", function (e) {
    e.preventDefault();

    if (!ievaditiDatumi) {
      alert("Vispirms ievadiet datumus!");
      return;
    }

    const no = $("input[name='no']").val();
    const lidz = $("input[name='lidz']").val();
    tekosaMeklesana = $("#meklet-lauks").val();

    // Filtra vākšana
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

    fetchIresanasMajas(tekosaMeklesana, filtri, tekosaKartosana, no, lidz);
  });

  // Kārtošanas opcija maiņa
  $(document).on("change", "#kartosanasOpcijasI", function () {
    if (!ievaditiDatumi) {
      alert("Vispirms ievadiet datumus!");
      return;
    }

    tekosaKartosana = $(this).val();
    const no = $("input[name='no']").val();
    const lidz = $("input[name='lidz']").val();

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

    fetchIresanasMajas(tekosaMeklesana, filtri, tekosaKartosana, no, lidz);
  });

  // Filtra tīrīšana
  $(document).on("click", "#izdest-filtrus-majas-iret", function (e) {
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

    const no = $("input[name='no']").val();
    const lidz = $("input[name='lidz']").val();

    fetchIresanasMajas("", {}, tekosaKartosana, no, lidz);
  });

  // Izveidot un nosūtīt veidlapu ar izvēlēto māju
  function izveidotUnNosutitFormu(majaId, no, lidz, galaCena) {
    const forma = document.createElement("form");
    forma.method = "POST";
    forma.action = "maja_iret.php";

    const lauki = { id: majaId, no: no, lidz: lidz, total: galaCena };

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

  // Noklikšķiniet uz sludinājumu - modalai logs ir atvērts
  $(document).on("click", ".sludinajumsIresanai", function () {
    let majaId = $(this).attr("maja_id");
    const no = $("input[name='no']").val();
    const lidz = $("input[name='lidz']").val();

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
    izveidotUnNosutitFormu(majaId, no, lidz, galaCena);
  });

  // Pogas saglabāt/dzēst no saglabātiem apstrāde
  $(document).on("click", ".saglabatSludinajumu", function (e) {
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
});
