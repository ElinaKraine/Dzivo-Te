$(document).ready(function () {
  fetchProfilaInfo();
  ieladetSludinajumus();
  $("#majoklaTips, #majoklaVeids").on("change", toggleFormFields);
  let editSludinajums = false;

  //#region Profila informācija
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
  // #endregion

  //#region Sludinājumi
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

          const klaseSarkans =
            ieraksts.statuss === "Atteikums" ? "sarkans" : "";
          const klaseZals =
            ieraksts.statuss === "Apsiprināts | Publicēts" ? "zals" : "";

          template += `
              <tr sludinajuma_ID="${ieraksts.id}" data-veids="${ieraksts.veids}">
                <td>${ieraksts.id}</td>
                <td>${ieraksts.majokla_tips}</td>
                <td>${ieraksts.veids}</td>
                <td>${ieraksts.pilseta}, ${ieraksts.iela} ${ieraksts.majas_numurs}</td>
                <td>${cenaFormateta}</td>
                <td>${ieraksts.platiba}</td>
                <td class='${klaseSarkans} ${klaseZals}'>${ieraksts.statuss}</td>
                <td>${ieraksts.izveidosanas_datums}</td>
                <td>
                  <a class="sludinajums-item editBtn"> <i class="fa fa-edit"></i> </a>    
                  <a class="sludinajums-delete deleteBtn"> <i class="fa fa-trash"></i> </a>
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

  function toggleFormFields() {
    const majoklaTips = $("#majoklaTips").val();
    const majoklaVeids = $("#majoklaVeids").val();

    if (majoklaTips === "maja") {
      $("#zemes-platiba").show();
      $("#maja-stavi").show();
      $("#dzivokla-numurs").hide();
      $("#dziv-stavs").hide();
    } else if (majoklaTips === "dzivoklis") {
      $("#zemes-platiba").hide();
      $("#maja-stavi").hide();
      $("#dzivokla-numurs").show();
      $("#dziv-stavs").show();
    }

    if (majoklaVeids === "pirkt") {
      $("#pirkt-cena").show();
      $(".iret-cena").hide();
    } else if (majoklaVeids === "iret") {
      $("#pirkt-cena").hide();
      $(".iret-cena").show();
    }
  }

  $(document).on("click", "#new-btn", (e) => {
    $(".modalSludinajums").css("display", "flex");
    toggleFormFields();
  });

  $(document).on("click", ".close-modal", (e) => {
    $(".modal").hide();
    $("#sludinajumaForma").trigger("reset");
    $("#pieteikumaForma").trigger("reset");
    edit = false;
  });

  //   $(document).on('click','.sludinajums-item', (e)=>{
  //     $(".modal").css('display','flex')

  //     const element = $(e.currentTarget).closest('tr')
  //     const id = $(element).attr("sludinajuma_ID")
  //     // console.log(id)

  //     $.post('./assets/database/sludinajumi_single.php',{id},(response) => {
  //         const sludinajums = JSON.parse(response)
  //         $('#majoklaTips').val(sludinajums.vards)
  //         $('#uzvards').val(sludinajums.uzvards)
  //         $('#epasts').val(sludinajums.epasts)
  //         $('#talrunis').val(sludinajums.talrunis)
  //         $('#apraksts').val(sludinajums.apraksts)
  //         $('#statuss').val(sludinajums.statuss)
  //         $('#piet_ID').val(sludinajums.id)
  //         $('#izveidots').text(sludinajums.datums);
  //         majoklaTips: $("#majoklaTips").val(),
  //         majoklaVeids: $("#majoklaVeids").val(),
  //         pilseta: $("#pilseta").val(),
  //         iela: $("#iela").val(),
  //         majasNumurs: $("#majasNumurs").val(),
  //         dzivoklaNumurs: $("#dzivoklaNumurs").val(),
  //         cenaPirkt: $("#cenaPirkt").val(),
  //         cenaDiena: $("#cenaDiena").val(),
  //         cenaNedela: $("#cenaNedela").val(),
  //         cenaMenesi: $("#cenaMenesi").val(),
  //         platiba: $("#platiba").val(),
  //         zemesPlatiba: $("#zemesPlatiba").val(),
  //         istabas: $("#istabas").val(),
  //         stavi: $("#stavi").val(),
  //         stavs: $("#stavs").val(),
  //         apraksts: $("#apraksts").val(),

  //         edit = true
  //     })
  // })

  // $("#sludinajumaForma").submit((e) => {
  //   e.preventDefault();
  //   const postData = {
  //     majoklaTips: $("#majoklaTips").val(),
  //     majoklaVeids: $("#majoklaVeids").val(),
  //     pilseta: $("#pilseta").val(),
  //     iela: $("#iela").val(),
  //     majasNumurs: $("#majasNumurs").val(),
  //     dzivoklaNumurs: $("#dzivoklaNumurs").val(),
  //     cenaPirkt: $("#cenaPirkt").val(),
  //     cenaDiena: $("#cenaDiena").val(),
  //     cenaNedela: $("#cenaNedela").val(),
  //     cenaMenesi: $("#cenaMenesi").val(),
  //     platiba: $("#platiba").val(),
  //     zemesPlatiba: $("#zemesPlatiba").val(),
  //     istabas: $("#istabas").val(),
  //     stavi: $("#stavi").val(),
  //     stavs: $("#stavs").val(),
  //     apraksts: $("#apraksts").val(),
  //   };

  //   // url = !edit ? 'database/pieteikums_add.php' : 'database/pieteikums_edit.php';
  //   url = "./assets/database/sludinajumi_add.php";
  //   console.log(postData, url);
  //   $.post(url, postData, () => {
  //     $(".modal").hide();
  //     $("#sludinajumaForma").trigger("reset");
  //     ieladetSludinajumus();
  //     // edit = false
  //   });
  // });
  $("#sludinajumaForma").submit((e) => {
    e.preventDefault();

    const formData = new FormData($("#sludinajumaForma")[0]);

    $.ajax({
      url: "./assets/database/sludinajumi_add.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        console.log("Server response:", response);
        $(".modal").hide();
        $("#sludinajumaForma").trigger("reset");
        ieladetSludinajumus();
      },
      error: function () {
        alert("Neizdevās nosūtīt sludinājumu!");
      },
    });
  });

  $(document).on("click", ".sludinajums-delete", (e) => {
    if (confirm("Vai tiesam velies dzest?")) {
      const element = $(e.currentTarget).closest("tr");
      const id = $(element).attr("sludinajuma_ID");
      const tabula = $(element).data("veids");
      // console.log(id)
      $.post(
        "./assets/database/sludinajumi_delete.php",
        { id, tabula },
        (response) => {
          ieladetSludinajumus();
        }
      );
    }
  });
  // #endregion

  //#region Mani pieteikumi
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
          const klaseSarkans =
            ieraksts.statuss === "Atteikums" ? "sarkans" : "";
          const klaseZals =
            ieraksts.statuss === "Mājoklis ir iegādāts" ? "zals" : "";
          template += `
              <tr ieraksta_ID="${ieraksts.id}">
                <td>${ieraksts.id}</td>
                <td>${ieraksts.majokla_tips}</td>
                <td>${ieraksts.pilseta}, ${ieraksts.iela} ${ieraksts.majas_numurs}</td>
                <td>${ieraksts.cena}</td>
                <td class='${klaseSarkans} ${klaseZals}'>${ieraksts.statuss}</td>
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

  $(document).on("click", ".ieraksts-delete", (e) => {
    if (confirm("Vai tiesam velies dzest?")) {
      const element = $(e.currentTarget).closest("tr");
      const id = $(element).attr("ieraksta_ID");
      // console.log(id)
      $.post("./assets/database/pieteikumi_delete.php", { id }, (response) => {
        ieladetPieteikumus();
      });
    }
  });

  // #endregion

  //#region Īre
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
  // #endregion

  //#region Lietotāju pieteikumi
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
          const klaseSarkans =
            ieraksts.statuss === "Atteikums" ? "sarkans" : "";
          const klaseZals =
            ieraksts.statuss === "Mājoklis ir iegādāts" ? "zals" : "";
          template += `
              <tr ieraksta_ID="${ieraksts.id}">
                <td>${ieraksts.id}</td>
                <td>${ieraksts.epasts}</td>
                <td>${ieraksts.majokla_tips}</td>
                <td>${ieraksts.pilseta}, ${ieraksts.iela} ${ieraksts.majas_numurs}</td>
                <td>${ieraksts.cena}</td>
                <td class='${klaseSarkans} ${klaseZals}'>${ieraksts.statuss}</td>
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

  $(document).on("click", ".ieraksts-item", (e) => {
    $(".modalStatuss").css("display", "flex");

    const element = $(e.currentTarget).closest("tr");
    const id = $(element).attr("ieraksta_ID");

    $.post(
      "./assets/database/liet_pieteikumi_single.php",
      { id },
      (response) => {
        const ieraksts = JSON.parse(response);
        $("#pietlietotajs").val(ieraksts.epasts);
        $("#pietMajoklaTips").val(ieraksts.majokla_tips);
        $("#pietAdrese").val(ieraksts.adrese);
        $("#pietCena").val(ieraksts.cena);
        $("#pietStatuss").val(ieraksts.statuss);
        $("#pieteikums_ID").val(ieraksts.id);
        $("#pietDatums").val(ieraksts.izveidosanas_datums);
      }
    );
  });

  $("#pieteikumaForma").submit((e) => {
    e.preventDefault();
    const postData = {
      statuss: $("#pietStatuss").val(),
      id: $("#pieteikums_ID").val(),
    };

    url = "./assets/database/liet_pieteikumi_edit.php";
    // console.log(postData, url);
    $.post(url, postData, () => {
      $(".modalStatuss").hide();
      $("#pieteikumaForma").trigger("reset");
      ieladetLietPieteikumus();
    });
  });

  // #endregion

  //#region Lietotāju īre
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
  // #endregion

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
