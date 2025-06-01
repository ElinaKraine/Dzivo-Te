$(document).ready(function () {
  fetchProfilaInfo();
  ieladetSludinajumus();
  $("#majoklaTips, #majoklaVeids").on("change", toggleFormFields);
  $("#nomainitAtteluSelect, #nomainitParoleSelect").on(
    "change",
    toggleFormFields2
  );
  $("#sludNomainitAtteliSelect").on("change", toggleSludAtteluSelect);
  let galleryImages = [];
  let currentImageIndex = 0;

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

  //#region Profila informācija
  function fetchProfilaInfo() {
    $.ajax({
      url: "./assets/database/profila_info.php",
      type: "GET",
      success: function (response) {
        const lietotaji = JSON.parse(response);
        let template = "";
        lietotaji.forEach((lietotajs) => {
          const attels = lietotajs.attels
            ? `<img src="data:image/jpeg;base64,${lietotajs.attels}" />`
            : '<div class="placeHolder"><i class="fa-solid fa-user"></i></div>';
          template += `
                        <div liet_ID="${lietotajs.id}">
                            <div class="kasteCentra">
                                <div class="profilaAttela">
                                    ${attels}
                                </div>
                                <h2>Sveiks, ${lietotajs.vards} ${lietotajs.uzvards}!</h2>
                            </div>
                            <div class="kastite">
                                <p><i class="fa-solid fa-envelope"></i> ${lietotajs.epasts}</p>
                                <p><i class="fa-solid fa-phone"></i> +371 ${lietotajs.talrunis}</p>
                                <a class="btn profila-item">Rediģēt profilu</a>
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

  function toggleFormFields2() {
    const nomainitParole = $("#nomainitParoleSelect").val();
    const nomainitAttelu = $("#nomainitAtteluSelect").val();

    if (nomainitParole === "ja") {
      $(".nomainitParole").show();
    } else if (nomainitParole === "ne") {
      $(".nomainitParole").hide();
    }

    if (nomainitAttelu === "ja") {
      $("#nomainitAttelu").show();
    } else if (nomainitAttelu === "ne") {
      $("#nomainitAttelu").hide();
    }
  }

  $(document).on("click", ".profila-item", (e) => {
    toggleFormFields2();
    $(".modalLietotajs").css("display", "flex");
    $("#lietFormPazinojums").text("");

    const element = $(e.currentTarget).closest("div[liet_ID]");
    const id = $(element).attr("liet_ID");

    $.post("./assets/database/profila_single.php", { id }, (response) => {
      const lietotajs = JSON.parse(response);
      $("#lietVards").val(lietotajs.vards);
      $("#lietUzvards").val(lietotajs.uzvards);
      $("#lietEpasts").val(lietotajs.epasts);
      $("#lietTalrunis").val(lietotajs.talrunis);
      $("#liet_ID").val(lietotajs.id);
    });
  });

  $("#lietotajaForma").submit((e) => {
    e.preventDefault();

    const formData = new FormData($("#lietotajaForma")[0]);

    $.ajax({
      url: "./assets/database/profila_edit.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (
          response.includes("Visi ievadas lauki") ||
          response.includes("Kļūda") ||
          response.includes("Parole") ||
          response.includes("jau")
        ) {
          $("#lietFormPazinojums").text(response);
          return;
        }

        if (response.includes("Profila")) {
          paradit_pazinojumu(response);
          fetchProfilaInfo();
          ieladetSludinajumus();
          return;
        }

        $(".modalLietotajs").hide();
        $("#lietotajaForma").trigger("reset");
        $("#lietFormPazinojums").text("");
        fetchProfilaInfo();
        ieladetSludinajumus();
      },
      error: function () {
        alert("Neizdevās saglabāt lietotāja informāciju!");
      },
    });
  });

  // #endregion

  //#region Sludinājumi
  function ieladetSludinajumus() {
    $.ajax({
      url: "./assets/database/sludinajumi_list.php",
      method: "GET",
      success: function (response) {
        const ieraksti = JSON.parse(response);
        let template = `<a class="addBtn" id="new-btn">Pievienot jaunu sludinājumu</a>`;

        if (ieraksti.length > 0) {
          template += `<table>
                        <tr>
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
        }

        if (ieraksti.length > 0) {
          ieraksti.forEach((ieraksts) => {
            const cenaFormateta =
              ieraksts.veids === "Iret"
                ? `${ieraksts.cena} €/mēnesī`
                : `${ieraksts.cena}`;

            const klaseSarkans =
              ieraksts.statuss === "Atteikums" ? "sarkans" : "";
            const klaseZals =
              ieraksts.statuss === "Apsiprināts | Publicēts" ? "zals" : "";
            const veids = ieraksts.veids === "Iret" ? "Īre" : "Pikrt";

            template += `
                <tr sludinajuma_ID="${ieraksts.id}" data-veids="${ieraksts.veids}">
                  <td>${ieraksts.majokla_tips}</td>
                  <td>${veids}</td>
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
        } else {
          template += `<p class='navRezultatus'>Jums vēl nav neviena sludinājuma</p>`;
        }

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
    $("#sludFormPazinojums").text("");
    $("#sludinajumaForma").trigger("reset");
    $(".nomainitAttelusRinda").hide();
    $(".nomainit-slud-atteli").show();
    $("#atteluGalerijaContainer").hide();
    $("#majoklaTips").show();
    $("#majoklaTips-text").hide();
    $("#id_sludinajums").val("");
    $("#sludinajums_saglabat").text("Izveidot");
    toggleFormFields();
  });

  $(document).on("click", ".close-modal", (e) => {
    const modalToClose = $(e.currentTarget).closest(".modal");

    if (modalToClose.attr("id") === "imageModal") {
      modalToClose.hide();
      return;
    }

    $(".modal").hide();
    $("#sludinajumaForma").trigger("reset");
    $("#pieteikumaForma").trigger("reset");
    edit = false;
  });

  $(document).on("click", ".sludinajums-item", (e) => {
    $(".modalSludinajums").css("display", "flex");
    $("#sludFormPazinojums").text("");

    const element = $(e.currentTarget).closest("tr");
    const id = $(element).attr("sludinajuma_ID");
    const veids = $(element).data("veids");

    $.post(
      "./assets/database/sludinajumi_single.php",
      { id, veids },
      (response) => {
        const data = JSON.parse(response);

        const majoklaTips =
          data.majokla_tips === "Mājas" ? "maja" : "dzivoklis";
        const majoklaTipsText = majoklaTips === "maja" ? "Māja" : "Dzīvoklis";

        $("#majoklaTips").val(majoklaTips);
        $("#majoklaTips").hide();
        $("#majoklaTips-text").text(majoklaTipsText).show();

        $("#majoklaVeids").val(veids.toLowerCase());
        $("#pilseta").val(data.pilseta);
        $("#iela").val(data.iela);
        $("#majasNumurs").val(data.majas_numurs);
        $("#dzivoklaNumurs").val(data.dzivokla_numurs || "");
        $("#cenaPirkt").val(data.cena || "");
        $("#cenaDiena").val(data.cena_diena || "");
        $("#cenaNedela").val(data.cena_nedela || "");
        $("#cenaMenesi").val(data.cena_menesis || "");
        $("#platiba").val(data.platiba);
        $("#zemesPlatiba").val(data.zemes_platiba || "");
        $("#istabas").val(data.istabas);
        $("#stavi").val(data.stavi || "");
        $("#stavs").val(data.stavs || "");
        $("#apraksts").val(data.apraksts || "");

        $("#id_sludinajums").val(id);
        $("#sludinajums_saglabat").text("Saglabāt");

        toggleFormFields();
        renderGallery(data.atteli);
        $("#atteluGalerijaContainer").show();
        $(".nomainitAttelusRinda").show();
        toggleSludAtteluSelect();
      }
    );
  });

  $("#sludinajumaForma").submit((e) => {
    e.preventDefault();

    const formData = new FormData($("#sludinajumaForma")[0]);

    const isEdit = $("[name='id_sludinajums']").val() !== "";

    const url = isEdit
      ? "./assets/database/sludinajumi_edit.php"
      : "./assets/database/sludinajumi_add.php";

    $.ajax({
      url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (
          response.includes("Visi ievadas lauki") ||
          response.includes("Kļūda") ||
          response.includes("Šāda adrese") ||
          response.includes("Šo sludinājumu")
        ) {
          $("#sludFormPazinojums").text(response);
          return;
        }

        if (response.includes("veiksmīgs")) {
          paradit_pazinojumu(response);
          ieladetSludinajumus();
          return;
        }

        $(".modal").hide();
        $("#sludinajumaForma").trigger("reset");
        $("#sludFormPazinojums").text("");
        ieladetSludinajumus();
      },
      error: function () {
        alert("Neizdevās nosūtīt sludinājumu!");
      },
    });
  });

  function toggleSludAtteluSelect() {
    const val = $("#sludNomainitAtteliSelect").val();
    if (val === "ja") {
      $(".nomainit-slud-atteli").show();
    } else {
      $(".nomainit-slud-atteli").hide();
    }
  }

  function renderGallery(images) {
    galleryImages = images;
    currentImageIndex = 0;

    const gallery = $("#atteluGalerija");
    gallery.empty();
    images.forEach((src, index) => {
      const imgEl = $(`
        <div class="viensAttela">
          <img src="data:image/jpeg;base64,${src}" />
        </div>
      `);
      imgEl.find("img").on("click", function () {
        currentImageIndex = index;
        showModalImage();
      });
      gallery.append(imgEl);
    });
  }

  function showModalImage() {
    const src = galleryImages[currentImageIndex];
    if (src) {
      $("#modalImage").attr("src", "data:image/jpeg;base64," + src);
      $("#imageModal").css("display", "flex");
    }
  }

  $("#prevImage").on("click", () => {
    currentImageIndex =
      (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
    showModalImage();
  });

  $("#nextImage").on("click", () => {
    currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
    showModalImage();
  });

  $(document).on("keydown", function (e) {
    if ($("#imageModal").is(":visible")) {
      if (e.key === "ArrowLeft") {
        $("#prevImage").click();
      } else if (e.key === "ArrowRight") {
        $("#nextImage").click();
      } else if (e.key === "Escape") {
        $("#imageModal").hide();
      }
    }
  });

  $(document).on("click", ".sludinajums-delete", (e) => {
    if (confirm("Vai esat pārliecināts, ka vēlaties dzēst šo sludinājumu?")) {
      const element = $(e.currentTarget).closest("tr");
      const id = $(element).attr("sludinajuma_ID");
      const tabula = $(element).data("veids");
      $.post(
        "./assets/database/sludinajumi_delete.php",
        { id, tabula },
        (response) => {
          if (response.includes("dzēst")) {
            paradit_pazinojumu(response);
            ieladetSludinajumus();
            return;
          }
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
        let template = "";
        if (ieraksti.length > 0) {
          template += `<table>
                        <tr>
                          <th>Mājokļa tips</th>
                          <th>Adrese</th>
                          <th>Cena (€)</th>
                          <th>Statuss</th>
                          <th>Datums</th>
                          <th></th>
                        </tr>
                        <tbody>`;
        }

        if (ieraksti.length > 0) {
          ieraksti.forEach((ieraksts) => {
            const klaseSarkans =
              ieraksts.statuss === "Atteikums" ? "sarkans" : "";
            const klaseZals =
              ieraksts.statuss === "Mājoklis ir iegādāts" ? "zals" : "";
            template += `
                <tr ieraksta_ID="${ieraksts.id}">
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
        } else {
          template += `<p class='navRezultatus'>Jums vēl nav neviena pieteikuma</p>`;
        }

        $("#tabula").html(template);
      },
      error: function () {
        alert("Neizdevas ieladet datus");
      },
    });
  }

  $(document).on("click", ".ieraksts-delete", (e) => {
    if (confirm("Vai esat pārliecināts, ka vēlaties dzēst šo pieteikumu?")) {
      const element = $(e.currentTarget).closest("tr");
      const id = $(element).attr("ieraksta_ID");
      $.post("./assets/database/pieteikumi_delete.php", { id }, (response) => {
        if (response.includes("nevar")) {
          paradit_pazinojumu(response);
          ieladetPieteikumus();
          return;
        }

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
        let template = "";
        if (ieraksti.length > 0) {
          template += `<table>
                        <tr>
                          <th>Mājokļa tips</th>
                          <th>Adrese</th>
                          <th>Iznomāts no</th>
                          <th>Iznomāts līdz</th>
                          <th>Cena (€)</th>
                          <th>Izveidošanas datums</th>
                        </tr>
                        <tbody>`;
        }

        if (ieraksti.length > 0) {
          ieraksti.forEach((ieraksts) => {
            template += `
                <tr>
                  <td>${ieraksts.majokla_tips}</td>
                  <td>${ieraksts.pilseta}, ${ieraksts.iela} ${ieraksts.majas_numurs}</td>
                  <td>${ieraksts.registresanas_datums}</td>
                  <td>${ieraksts.izrakstisanas_datums}</td>
                  <td>${ieraksts.cena}</td>
                  <td>${ieraksts.izveidosanas_datums}</td>
                </tr>`;
          });

          template += `</tbody></table>`;
        } else {
          template += `<p class='navRezultatus'>Jums vēl nav īres ieraksta</p>`;
        }

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
        let template = "";
        if (ieraksti.length > 0) {
          template += `<table>
                        <tr>
                          <th>Lietotājs</th>
                          <th>Mājokļa tips</th>
                          <th>Adrese</th>
                          <th>Cena (€)</th>
                          <th>Statuss</th>
                          <th>Datums</th>
                          <th></th>
                        </tr>
                        <tbody>`;
        }

        if (ieraksti.length > 0) {
          ieraksti.forEach((ieraksts) => {
            const klaseSarkans =
              ieraksts.statuss === "Atteikums" ? "sarkans" : "";
            const klaseZals =
              ieraksts.statuss === "Mājoklis ir iegādāts" ? "zals" : "";
            template += `
                <tr ieraksta_ID="${ieraksts.id}">
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
        } else {
          template += `<p class='navRezultatus'>Nav iesniegts neviens pieteikums</p>`;
        }

        $("#tabula").html(template);
      },
      error: function () {
        alert("Neizdevas ieladet datus");
      },
    });
  }

  $(document).on("click", ".ieraksts-item", (e) => {
    $(".modalStatuss").css("display", "flex");
    $("#pietFormPazinojums").text("");

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
    $.post(url, postData, (response) => {
      if (response.includes("nevar")) {
        $("#pietFormPazinojums").text(response);
        return;
      }

      if (response.includes("veiksmīgi")) {
        paradit_pazinojumu(response);
        ieladetLietPieteikumus();
        return;
      }

      $(".modalStatuss").hide();
      $("#pieteikumaForma").trigger("reset");
      $("#pietFormPazinojums").text("");
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
        let template = "";
        if (ieraksti.length > 0) {
          template += `<table>
                        <tr>
                          <th>Lietotājs</th>
                          <th>Mājokļa tips</th>
                          <th>Adrese</th>
                          <th>Iznomāts no</th>
                          <th>Iznomāts līdz</th>
                          <th>Cena (€)</th>
                          <th>Datums</th>
                        </tr>
                        <tbody>`;
        }

        if (ieraksti.length > 0) {
          ieraksti.forEach((ieraksts) => {
            template += `
                <tr>
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
        } else {
          template += `<p class='navRezultatus'>Nav neviena ieraksta</p>`;
        }

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
