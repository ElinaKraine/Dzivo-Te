$(document).ready(function () {
  let tekosaMeklesana = "";
  let tekosaKartosana = "datums_desc";
  let ievaditiDatumi = false;
  const no = $("input[name='no']").val();
  const lidz = $("input[name='lidz']").val();

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
    const dienas =
      Math.ceil((datumsLidz - datumsNo) / (1000 * 60 * 60 * 24)) + 1;
    return dienas;
  }

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

    $.ajax({
      url: `./assets/database/pieejamie_majas_list.php?${queryParams.toString()}`,
      type: "GET",
      success: function (response) {
        const majas = JSON.parse(response);
        let template = "";

        if (majas.length > 0) {
          majas.forEach((maja) => {
            const galaCena = galigaCena(
              no,
              lidz,
              parseFloat(maja.cena_diena),
              parseFloat(maja.cena_nedela),
              parseFloat(maja.cena_menesis)
            );
            const parDienas = dienasSkaitsFunkcija(no, lidz);
            template += `
                <div class='sludinajums sludinajumsIresanai' 
                  maja_id="${maja.id}"
                  data-cena_diena="${maja.cena_diena}" 
                  data-cena_nedela="${maja.cena_nedela}" 
                  data-cena_menesis="${maja.cena_menesis}"
                >
                  <div class='attela-sirds'>
                    <img src="data:image/jpeg;base64,${maja.pirma_attela}" />
                  </div>
                  <p id='cena'>${galaCena} € par ${parDienas} dienas</p>
                  <div id='papildInfo'>
                    <p><i class='fa-solid fa-door-open'></i>${maja.istabas}</p>
                    <p><i class='fa-solid fa-ruler-combined'></i> ${maja.platiba} m<sup>2</sup></p>
                    <p><i class='fa-solid fa-stairs'></i> ${maja.stavi}</p>
                  </div>
                  <p id='adrese'>${maja.pilseta}, ${maja.iela} ${maja.majas_numurs}</p>
                </div>
              `;
          });

          $(".majasSaturs.iresanasBack").removeClass("iresanasBack");
        } else {
          template =
            "<p class='navRezultatus'>Nav pieejamu mājokļu izvēlētajās dienās.</p>";
        }

        $("#majasIret").html(template);
      },
      error: function () {
        alert("Kļūda ielādējot pieejamās mājas.");
      },
    });
  }

  $(document).on("submit", ".iresanasDatumi", function (e) {
    e.preventDefault();

    const no = $("input[name='no']").val();
    const lidz = $("input[name='lidz']").val();

    if (!no || !lidz) {
      alert("Lūdzu, ievadiet abus datumus!");
      return;
    }

    ievaditiDatumi = true;

    tekosaMeklesana = $("#meklet-lauks").val();

    fetchIresanasMajas(tekosaMeklesana, {}, tekosaKartosana, no, lidz);
  });

  $(document).on("click", ".mekleteFiltrusI", function (e) {
    e.preventDefault();

    if (!ievaditiDatumi) {
      alert("Vispirms ievadiet datumus!");
      return;
    }

    const no = $("input[name='no']").val();
    const lidz = $("input[name='lidz']").val();

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

    fetchIresanasMajas(tekosaMeklesana, filtri, tekosaKartosana, no, lidz);
  });

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

  function izveidotUnNosutitFormu(majaId, no, lidz, galaCena) {
    const forma = document.createElement("form");
    forma.method = "POST";
    forma.action = "maja_iret.php";

    const lauki = {
      id: majaId,
      no: no,
      lidz: lidz,
      total: galaCena,
    };

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
});
