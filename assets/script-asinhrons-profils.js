$(document).ready(function () {
  fetchProfilaInfo();
  ieladetSludinajumus();

  function fetchProfilaInfo() {
    $.ajax({
      url: "./assets/database/profila_info.php",
      type: "GET",
      success: function (response) {
        const lietotaji = JSON.parse(response);
        let template = "";
        lietotaji.forEach((lietotajs) => {
          template += `
                            <div liet_ID="${lietotajs.id}">
                                <div class="kasteCentra">
                                    <div class="profilaAttela">
                                        <img src="data:image/jpeg;base64,${lietotajs.attels}" />
                                    </div>
                                    <h2>Sveiks, ${lietotajs.vards} ${lietotajs.uzvards}!</h2>
                                </div>
                                <div class="kastite">
                                    <p><i class="fa-solid fa-envelope"></i> ${lietotajs.epasts}</p>
                                    <p><i class="fa-solid fa-phone"></i> +371 ${lietotajs.talrunis}</p>
                                    <a href="./" class="btn">Rediģēt profilu</a>
                                </div>
                            </div>
                        `;
        });
        $("#profila_info").html(template);
      },
      error: function () {
        alert("Neizdevas ieladet datus");
      },
    });
  }

  function ieladetSludinajumus() {
    $.ajax({
      url: "./assets/database/sludinajumi_list.php",
      method: "GET",
      success: function (response) {
        const ieraksti = JSON.parse(response);
        let template = `<a class="addBtn" id="new-btn">Pievienot jaunu sludinājumu</a>
        <table>
          <tr>
            <th>ID</th>
            <th>Mājokļa tips</th>
            <th>Veids</th>
            <th>Adrese</th>
            <th>Cena (€)</th>
            <th>Platība (m<sup>2</sup>)</th>
            <th>Statuss</th>
            <th>Datums</th>
            <th></th>
          </tr>
          <tbody>`;

        ieraksti.forEach((ieraksts) => {
          const cenaFormateta =
            ieraksts.veids === "Iret"
              ? `${ieraksts.cena} €/mēnesī`
              : `${ieraksts.cena}`;

          template += `
              <tr ieraksta_ID="${ieraksts.id}">
                <td>${ieraksts.id}</td>
                <td>${ieraksts.majokla_tips}</td>
                <td>${ieraksts.veids}</td>
                <td>${ieraksts.pilseta}, ${ieraksts.iela} ${ieraksts.majas_numurs}</td>
                <td>${cenaFormateta}</td>
                <td>${ieraksts.platiba}</td>
                <td>${ieraksts.statuss}</td>
                <td>${ieraksts.izveidosanas_datums}</td>
                <td>
                  <a class="ieraksts-item editBtn"> <i class="fa fa-edit"></i> </a>    
                  <a class="ieraksts-delete deleteBtn"> <i class="fa fa-trash"></i> </a>
                </td>
              </tr>`;
        });

        template += `</tbody></table>`;
        $("#tabula").html(template);
      },
      error: function () {
        alert("Neizdevas ieladet datus");
      },
    });
  }

  function ieladetPieteikumus() {
    $.ajax({
      url: "./assets/database/pieteikumi_list.php",
      method: "GET",
      success: function (response) {
        const ieraksti = JSON.parse(response);
        let template = `
        <table>
          <tr>
            <th>ID</th>
            <th>Mājokļa tips</th>
            <th>Adrese</th>
            <th>Cena (€)</th>
            <th>Statuss</th>
            <th>Datums</th>
            <th></th>
          </tr>
          <tbody>`;

        ieraksti.forEach((ieraksts) => {
          template += `
              <tr ieraksta_ID="${ieraksts.id}">
                <td>${ieraksts.id}</td>
                <td>${ieraksts.majokla_tips}</td>
                <td>${ieraksts.pilseta}, ${ieraksts.iela} ${ieraksts.majas_numurs}</td>
                <td>${ieraksts.cena}</td>
                <td>${ieraksts.statuss}</td>
                <td>${ieraksts.izveidosanas_datums}</td>
                <td>  
                  <a class="ieraksts-delete deleteBtn"> <i class="fa fa-trash"></i> </a>
                </td>
              </tr>`;
        });

        template += `</tbody></table>`;
        $("#tabula").html(template);
      },
      error: function () {
        alert("Neizdevas ieladet datus");
      },
    });
  }

  function ieladetIri() {
    $.ajax({
      url: "./assets/database/ire_list.php",
      method: "GET",
      success: function (response) {
        const ieraksti = JSON.parse(response);
        let template = `
        <table>
          <tr>
            <th>ID</th>
            <th>Mājokļa tips</th>
            <th>Adrese</th>
            <th>Iznomāts no</th>
            <th>Iznomāts līdz</th>
            <th>Cena (€)</th>
            <th>Izveidošanas datums</th>
          </tr>
          <tbody>`;

        ieraksti.forEach((ieraksts) => {
          template += `
              <tr>
                <td>${ieraksts.id}</td>
                <td>${ieraksts.majokla_tips}</td>
                <td>${ieraksts.pilseta}, ${ieraksts.iela} ${ieraksts.majas_numurs}</td>
                <td>${ieraksts.registresanas_datums}</td>
                <td>${ieraksts.izrakstisanas_datums}</td>
                <td>${ieraksts.cena}</td>
                <td>${ieraksts.izveidosanas_datums}</td>
              </tr>`;
        });

        template += `</tbody></table>`;
        $("#tabula").html(template);
      },
      error: function () {
        alert("Neizdevas ieladet datus");
      },
    });
  }

  function ieladetLietPieteikumus() {
    $.ajax({
      url: "./assets/database/liet_pieteikumi_list.php",
      method: "GET",
      success: function (response) {
        const ieraksti = JSON.parse(response);
        let template = `
        <table>
          <tr>
            <th>ID</th>
            <th>Lietotājs</th>
            <th>Mājokļa tips</th>
            <th>Adrese</th>
            <th>Cena (€)</th>
            <th>Statuss</th>
            <th>Datums</th>
            <th></th>
          </tr>
          <tbody>`;

        ieraksti.forEach((ieraksts) => {
          template += `
              <tr ieraksta_ID="${ieraksts.id}">
                <td>${ieraksts.id}</td>
                <td>${ieraksts.epasts}</td>
                <td>${ieraksts.majokla_tips}</td>
                <td>${ieraksts.pilseta}, ${ieraksts.iela} ${ieraksts.majas_numurs}</td>
                <td>${ieraksts.cena}</td>
                <td>${ieraksts.statuss}</td>
                <td>${ieraksts.izveidosanas_datums}</td>
                <td>
                  <a class="ieraksts-item editBtn"> <i class="fa fa-edit"></i> </a>
                </td>
              </tr>`;
        });

        template += `</tbody></table>`;
        $("#tabula").html(template);
      },
      error: function () {
        alert("Neizdevas ieladet datus");
      },
    });
  }

  function ieladetLietIri() {
    $.ajax({
      url: "./assets/database/liet_ire_list.php",
      method: "GET",
      success: function (response) {
        const ieraksti = JSON.parse(response);
        let template = `
        <table>
          <tr>
            <th>ID</th>
            <th>Lietotājs</th>
            <th>Mājokļa tips</th>
            <th>Adrese</th>
            <th>Iznomāts no</th>
            <th>Iznomāts līdz</th>
            <th>Cena (€)</th>
            <th>Datums</th>
          </tr>
          <tbody>`;

        ieraksti.forEach((ieraksts) => {
          template += `
              <tr>
                <td>${ieraksts.id}</td>
                <td>${ieraksts.epasts}</td>
                <td>${ieraksts.majokla_tips}</td>
                <td>${ieraksts.pilseta}, ${ieraksts.iela} ${ieraksts.majas_numurs}</td>
                <td>${ieraksts.registresanas_datums}</td>
                <td>${ieraksts.izrakstisanas_datums}</td>
                <td>${ieraksts.cena}</td>
                <td>${ieraksts.izveidosanas_datums}</td>
              </tr>`;
        });

        template += `</tbody></table>`;
        $("#tabula").html(template);
      },
      error: function () {
        alert("Neizdevas ieladet datus");
      },
    });
  }

  $(".tabulaPoga").on("click", function () {
    $(".tabulaPoga").removeClass("atlasitaTabula");
    $(this).addClass("atlasitaTabula");

    const tabName = $(this).data("tab");

    switch (tabName) {
      case "sludinajumi_list":
        ieladetSludinajumus();
        break;
      case "pieteikumi_list":
        ieladetPieteikumus();
        break;
      case "ire_list":
        ieladetIri();
        break;
      case "liet_pieteikumi_list":
        ieladetLietPieteikumus();
        break;
      case "liet_ire_list":
        ieladetLietIri();
        break;
    }
  });
});
