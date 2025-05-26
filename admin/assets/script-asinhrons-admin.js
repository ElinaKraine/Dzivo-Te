$(document).ready(function () {
  fetchProfilaInfo();
  fetchDazadaInfo();
  fetchGrafiks();
  fetchTabulaLietotaji();
  fetchTabulaAdminLietotaji();
  fetchSludinajumi();
  fetchPieteikumi();
  fetchIresIeraksti();
  $("#nomainitAtteluSelectAdmin, #nomainitParoleSelectAdmin").on(
    "change",
    toggleFormFields
  );
  $("#nomainitAtteluSelectTabulaAdmin, #nomainitParoleSelectTabulaAdmin").on(
    "change",
    toggleFormFields2
  );
  $("#majoklaTipsAdmin, #majoklaVeidsAdmin").on("change", toggleFormFields3);
  $("#sludNomainitAtteliSelectAdmin").on("change", toggleSludAtteluSelect);
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
      url: "../assets/database/profila_info.php",
      type: "GET",
      success: function (response) {
        const lietotaji = JSON.parse(response);
        let template = "";
        lietotaji.forEach((lietotajs) => {
          template += `
                        <div liet_admin_ID="${lietotajs.id}" class="lietotajaKaste">
                            <div class="kasteCentra">
                                <div class="profilaAttela">
                                    <img src="data:image/jpeg;base64,${lietotajs.attels}" />
                                </div>
                            </div>
                            <div class="kastite">
                                <h2>Sveiks, ${lietotajs.vards} ${lietotajs.uzvards}!</h2>
                                <p><i class="fa-solid fa-envelope"></i> ${lietotajs.epasts}</p>
                                <p><i class="fa-solid fa-phone"></i> +371 ${lietotajs.talrunis}</p>
                                <a class="btn profila-admin-item">Rediģēt profilu</a>
                            </div>
                        </div>
                    `;
        });
        $("#profila_admin_info").html(template);
      },
      error: function () {
        alert("Neizdevas ieladet datus");
      },
    });
  }

  function toggleFormFields() {
    const nomainitParole = $("#nomainitParoleSelectAdmin").val();
    const nomainitAttelu = $("#nomainitAtteluSelectAdmin").val();

    if (nomainitParole === "ja") {
      $(".nomainitParoleAdmin").show();
    } else if (nomainitParole === "ne") {
      $(".nomainitParoleAdmin").hide();
    }

    if (nomainitAttelu === "ja") {
      $("#nomainitAtteluAdmin").show();
    } else if (nomainitAttelu === "ne") {
      $("#nomainitAtteluAdmin").hide();
    }
  }

  $(document).on("click", ".profila-admin-item", (e) => {
    $(".modalLietotajsAdmin").css("display", "flex");
    toggleFormFields();

    const element = $(e.currentTarget).closest("div[liet_admin_ID]");
    const id = $(element).attr("liet_admin_ID");

    $.post("../assets/database/profila_single.php", { id }, (response) => {
      const lietotajs = JSON.parse(response);
      $("#lietVardsAdmin").val(lietotajs.vards);
      $("#lietUzvardsAdmin").val(lietotajs.uzvards);
      $("#lietEpastsAdmin").val(lietotajs.epasts);
      $("#lietTalrunisAdmin").val(lietotajs.talrunis);
      $("#liet_admin_ID").val(lietotajs.id);
    });
  });

  $("#lietotajaFormaAdmin").submit((e) => {
    e.preventDefault();

    const formData = new FormData($("#lietotajaFormaAdmin")[0]);

    $.ajax({
      url: "../assets/database/profila_edit.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        $(".modalLietotajsAdmin").hide();
        $("#lietotajaFormaAdmin").trigger("reset");
        fetchProfilaInfo();
      },
      error: function () {
        alert("Neizdevās saglabāt lietotāja informāciju!");
      },
    });
  });

  $(document).on("click", ".close-modal", (e) => {
    $(".modal").hide();
  });

  // #endregion

  //#region Dažāda informācija
  function fetchDazadaInfo() {
    $.ajax({
      url: "./database/dazada_info.php",
      type: "GET",
      dataType: "json",
      success: function (info) {
        const template = `
        <div class="dazadaInfo">
            <div class="rinda">
                <div class="kaste">
                    <div class="ciparsApraksts">
                        <h2>${info.sludinajumuSkaits}</h2>
                        <p>Mājokļi</p>
                    </div>
                    <div class="iconKaste">
                        <i class="fa-solid fa-house"></i>
                    </div>
                </div>
                <div class="kaste">
                    <div class="ciparsApraksts">
                        <h2>${info.rezervacijuSkaits}</h2>
                        <p>Rezervācijas (24h)</p>
                    </div>
                    <div class="iconKaste">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
            </div>
            <div class="rinda">
                <div class="kaste">
                    <div class="ciparsApraksts">
                        <h2>${info.pardotoSkaits}</h2>
                        <p>Pārdošanas (24h)</p>
                    </div>
                    <div class="iconKaste">
                        <i class="fa-solid fa-hands"></i>
                    </div>
                </div>
                <div class="kaste">
                    <div class="ciparsApraksts">
                        <h2>${info.pelna}</h2>
                        <p>Peļņa (24h)</p>
                    </div>
                    <div class="iconKaste">
                        <i class="fa-solid fa-euro-sign"></i>
                    </div>
                </div>
            </div>
        </div>
        `;
        $("#dazada_info").html(template);
      },
      error: function () {
        alert("Neizdevās ielādēt dažādo informāciju.");
      },
    });
  }

  // #endregion

  //#region Grafiks
  function fetchGrafiks() {
    $.ajax({
      url: "./database/grafiks_data.php",
      type: "GET",
      dataType: "json",
      success: function (data) {
        const labels = data.map((item) => item.date);
        const pieteikumi = data.map((item) => item.pieteikumi);
        const rezervacijas = data.map((item) => item.rezervacijas);
        const sludinajumi = data.map((item) => item.sludinajumi);

        const ctx = document.createElement("canvas");
        document.getElementById("grafiks").innerHTML = "";
        document.getElementById("grafiks").appendChild(ctx);

        new Chart(ctx, {
          type: "line",
          data: {
            labels: labels,
            datasets: [
              {
                label: "Pieteikumi",
                data: pieteikumi,
                borderColor: "blue",
                fill: false,
                tension: 0.3,
              },
              {
                label: "Rezervācijas",
                data: rezervacijas,
                borderColor: "green",
                fill: false,
                tension: 0.3,
              },
              {
                label: "Sludinājumi",
                data: sludinajumi,
                borderColor: "red",
                fill: false,
                tension: 0.3,
              },
            ],
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: "top",
              },
              title: {
                display: true,
                text: "Statistika par pēdējām 7 dienām",
              },
            },
            scales: {
              y: {
                beginAtZero: true,
                suggestedMax: 5,
                ticks: {
                  stepSize: 1,
                },
              },
            },
          },
        });
      },
      error: function () {
        alert("Neizdevās ielādēt grafiku.");
      },
    });
  }
  // #endregion

  //#region Lietotāji Tabula index.php
  function fetchTabulaLietotaji() {
    $.ajax({
      url: "./database/lietotaji_list.php",
      type: "GET",
      success: function (response) {
        const lietotajiTabula = JSON.parse(response);
        let template = "";
        lietotajiTabula.forEach((lietotajs) => {
          template += `
                        <div class="lietotajsKaste">
                            <div class="lietotajaAttels">
                                <img src="data:image/jpeg;base64,${lietotajs.attels}" />
                            </div>
                            <div class="vardsUzvardsEpasts">
                                <p>${lietotajs.vards} ${lietotajs.uzvards}</p>
                                <p>${lietotajs.epasts}</p>
                            </div>
                        </div>
                    `;
        });
        $("#lietotaji_tabula_admin").html(template);
      },
      error: function () {
        alert("Neizdevas ieladet datus");
      },
    });
  }

  // #endregion

  //#region Lietotāji Tabula lietotaji.php
  function fetchTabulaAdminLietotaji() {
    $.ajax({
      url: "./database/lietotaji_list.php",
      type: "GET",
      success: function (response) {
        const lietotajiTabula = JSON.parse(response);
        let template = "";
        lietotajiTabula.forEach((lietotajsTabula) => {
          template += `
                        <tr lietotajs_admin_ID="${lietotajsTabula.id}">
                            <td>${lietotajsTabula.id}</td>
                            <td>${lietotajsTabula.vards} ${lietotajsTabula.uzvards}</td>
                            <td>${lietotajsTabula.epasts}</td>
                            <td>${lietotajsTabula.talrunis}</td>
                            <td>${lietotajsTabula.loma}</td>
                            <td>${lietotajsTabula.izveidosanas_datums}</td>
                            <td class="items">
                                <a class="lietotajs-tabula-item editBtn"> <i class="fa fa-edit"></i> </a>    
                                <a class="lietotajs-tabula-delete deleteBtn"> <i class="fa fa-trash"></i> </a>
                            </td>
                        </tr>
                    `;
        });
        $("#lietotaji").html(template);
      },
      error: function () {
        alert("Neizdevas ieladet datus");
      },
    });
  }

  function toggleFormFields2() {
    const nomainitParole = $("#nomainitParoleSelectTabulaAdmin").val();
    const nomainitAttelu = $("#nomainitAtteluSelectTabulaAdmin").val();

    if (nomainitParole === "ja") {
      $(".nomainitParoleTabulaAdmin").show();
    } else if (nomainitParole === "ne") {
      $(".nomainitParoleTabulaAdmin").hide();
    }

    if (nomainitAttelu === "ja") {
      $("#nomainitAtteluTabulaAdmin").show();
    } else if (nomainitAttelu === "ne") {
      $("#nomainitAtteluTabulaAdmin").hide();
    }
  }

  $(document).on("click", ".lietotajs-tabula-item", (e) => {
    $(".modalLietotajsTabulaAdmin").css("display", "flex");
    $(".nomainitParoleTabulaAdminRinda").show();
    $(".nomainitParoleTabulaAdmin").hide();
    $(".nomainitAtteluTabulaAdminRinda").show();
    $("#nomainitAtteluTabulaAdmin").hide();
    $(".papildInfoLiet").show();
    toggleFormFields2();

    const element = $(e.currentTarget).closest("tr");
    const id = $(element).attr("lietotajs_admin_ID");

    $.post("../assets/database/profila_single.php", { id }, (response) => {
      const lietotajsTabula = JSON.parse(response);
      $("#lietVardsTabulaAdmin").val(lietotajsTabula.vards);
      $("#lietUzvardsTabulaAdmin").val(lietotajsTabula.uzvards);
      $("#lietEpastsTabulaAdmin").val(lietotajsTabula.epasts);
      $("#lietTalrunisTabulaAdmin").val(lietotajsTabula.talrunis);
      $("#lomaSelect").val(lietotajsTabula.loma);
      $("#atjauninasanasDatums").text(lietotajsTabula.atjauninasanas_datums);
      $("#ipAdrese").text(lietotajsTabula.ip_adrese);
      $("#lietotajs_admin_ID").val(lietotajsTabula.id);
    });
  });

  $(document).on("click", "#new-btn-lietotajs", (e) => {
    $(".modalLietotajsTabulaAdmin").css("display", "flex");
    $("#lietotajaFormaTabulaAdmin").trigger("reset");
    $(".nomainitParoleTabulaAdminRinda").hide();
    $(".nomainitParoleTabulaAdmin").show();
    $(".nomainitAtteluTabulaAdminRinda").hide();
    $("#nomainitAtteluTabulaAdmin").show();
    $(".papildInfoLiet").hide();
    $("#lietotajs_admin_ID").val("");
  });

  $("#lietotajaFormaTabulaAdmin").submit((e) => {
    e.preventDefault();

    const formData = new FormData($("#lietotajaFormaTabulaAdmin")[0]);

    const editLietotajs = $("#lietotajs_admin_ID").val() !== "";
    const url = editLietotajs
      ? "./database/lietotaji_edit.php"
      : "./database/lietotaji_add.php";

    $.ajax({
      url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        $(".modalLietotajsTabulaAdmin").hide();
        $("#lietotajaFormaTabulaAdmin").trigger("reset");
        fetchTabulaAdminLietotaji();
      },
      error: function () {
        alert("Neizdevās saglabāt lietotāja informāciju!");
      },
    });
  });

  $(document).on("click", ".lietotajs-tabula-delete", (e) => {
    if (confirm("Vai esat pārliecināts, ka vēlaties dzēst šo lietotāju?")) {
      const element = $(e.currentTarget).closest("tr");
      const id = $(element).attr("lietotajs_admin_ID");

      $.post("./database/lietotaji_delete.php", { id }, (response) => {
        fetchTabulaAdminLietotaji();
      });
    }
  });

  // #endregion

  //#region Sludinājumi
  function fetchSludinajumi() {
    $.ajax({
      url: "./database/sludinajumi_list.php",
      type: "GET",
      success: function (response) {
        const sludinajumi = JSON.parse(response);
        const rowsPerPage = 10;
        let currentPage = 1;

        function renderTable(page) {
          let template = "";
          const start = (page - 1) * rowsPerPage;
          const end = start + rowsPerPage;
          const pageItems = sludinajumi.slice(start, end);

          pageItems.forEach((sludinajums) => {
            const cenaFormateta =
              sludinajums.veids === "Iret"
                ? `${sludinajums.cena} €/mēnesī`
                : `${sludinajums.cena}`;
            const klaseSarkans =
              sludinajums.statuss === "Dzēsts" ? "sarkans" : "";
            const klaseZals =
              sludinajums.statuss === "Apsiprināts | Publicēts" ? "zals" : "";
            const klaseJauns =
              sludinajums.statuss === "Iesniegts sludinājums" ? "jauns" : "";

            template += `
              <tr slud_ID="${sludinajums.id}" data-veids="${sludinajums.veids}" class='${klaseJauns}'>
                <td>${sludinajums.id}</td>
                <td>${sludinajums.majokla_tips}</td>
                <td>${sludinajums.veids}</td>
                <td>${sludinajums.epasts}</td>
                <td>${sludinajums.adrese}</td>
                <td>${cenaFormateta}</td>
                <td>${sludinajums.platiba}</td>
                <td>${sludinajums.izveidosanas_datums}</td>
                <td class='${klaseSarkans} ${klaseZals}'>${sludinajums.statuss}</td>
                <td class="items">
                  <a class="sludinajums-item editBtn"> <i class="fa fa-edit"></i> </a>    
                  <a class="sludinajums-delete deleteBtn"> <i class="fa fa-trash"></i> </a>
                </td>
              </tr>`;
          });

          $("#sludinajumi-saraksts").html(template);
        }

        function renderPagination() {
          const totalPages = Math.ceil(sludinajumi.length / rowsPerPage);
          let buttons = "";

          for (let i = 1; i <= totalPages; i++) {
            buttons += `<button class="${
              i === currentPage ? "active" : ""
            }" data-page="${i}">${i}</button>`;
          }

          $("#pagination").html(buttons);
        }

        $("#pagination").on("click", "button", function () {
          currentPage = parseInt($(this).attr("data-page"));
          renderTable(currentPage);
          renderPagination();
        });

        renderTable(currentPage);
        renderPagination();
      },
      error: function () {
        alert("Neizdevas ieladet datus");
      },
    });
  }

  function toggleFormFields3() {
    const majoklaTips = $("#majoklaTipsAdmin").val();
    const majoklaVeids = $("#majoklaVeidsAdmin").val();

    if (majoklaTips === "maja") {
      $("#zemes-platiba-admin").show();
      $("#zemesPlatibaAdmin").prop("disabled", false);
      $("#maja-stavi-admin").show();
      $("#dzivokla-numurs-admin").hide();
      $("#dziv-stavs-admin").hide();
    } else if (majoklaTips === "dzivoklis") {
      $("#zemes-platiba-admin").hide();
      $("#zemesPlatibaAdmin").prop("disabled", true);
      $("#maja-stavi-admin").hide();
      $("#dzivokla-numurs-admin").show();
      $("#dziv-stavs-admin").show();
    }

    if (majoklaVeids === "pirkt") {
      $("#pirkt-cena-admin").show();
      $(".iret-cena").hide();
    } else if (majoklaVeids === "iret") {
      $("#pirkt-cena-admin").hide();
      $(".iret-cena").show();
    }
  }

  $(document).on("click", "#new-btn-slud", (e) => {
    $(".modalSludinajums").css("display", "flex");
    $("#sludinajumaFormaAdmin").trigger("reset");
    $("#sludFormPazinojumsAdmin").text("");
    $(".nomainitAttelusRinda").hide();
    $(".nomainit-slud-atteli").show();
    $("#atteluGalerijaContainerAdmin").hide();
    $("#majoklaTipsAdmin").show();
    $("#majoklaTips-text-admin").hide();
    $("#slud_ID").val("");
    $("#sludinajums_saglabat_admin").text("Izveidot");
    toggleFormFields3();
  });

  $(document).on("click", ".close-modal", (e) => {
    const modalToClose = $(e.currentTarget).closest(".modal");

    if (modalToClose.attr("id") === "imageModal") {
      modalToClose.hide();
      return;
    }

    $(".modal").hide();
    $("#sludinajumaFormaAdmin").trigger("reset");
    edit = false;
  });

  $(document).on("click", ".sludinajums-item", (e) => {
    $(".modalSludinajums").css("display", "flex");
    $("#sludFormPazinojumsAdmin").text("");

    const element = $(e.currentTarget).closest("tr");
    const id = $(element).attr("slud_ID");
    const veids = $(element).data("veids");

    $.post(
      "../assets/database/sludinajumi_single.php",
      { id, veids },
      (response) => {
        const data = JSON.parse(response);

        const majoklaTips =
          data.majokla_tips === "Mājas" ? "maja" : "dzivoklis";
        const majoklaTipsText = majoklaTips === "maja" ? "Māja" : "Dzīvoklis";

        $("#majoklaTipsAdmin").val(majoklaTips);
        $("#majoklaTipsAdmin").hide();
        $("#majoklaTips-text-admin").text(majoklaTipsText).show();

        $("#majoklaVeidsAdmin").val(veids.toLowerCase());
        $("#pilsetaAdmin").val(data.pilseta);
        $("#ielaAdmin").val(data.iela);
        $("#majasNumursAdmin").val(data.majas_numurs);
        $("#dzivoklaNumursAdmin").val(data.dzivokla_numurs || "");
        $("#cenaPirktAdmin").val(data.cena || "");
        $("#cenaDienaAdmin").val(data.cena_diena || "");
        $("#cenaNedelaAdmin").val(data.cena_nedela || "");
        $("#cenaMenesiAdmin").val(data.cena_menesis || "");
        $("#platibaAdmin").val(data.platiba);
        $("#zemesPlatibaAdmin").val(data.zemes_platiba || "");
        $("#istabasAdmin").val(data.istabas);
        $("#staviAdmin").val(data.stavi || "");
        $("#stavsAdmin").val(data.stavs || "");
        $("#aprakstsAdmin").val(data.apraksts || "");
        $("#sludNomainitStatusuAdmin").val(data.statuss);
        $("#ipAdreseSlud").text(data.ip_adrese);
        $("#atjauninasanasDatumsSlud").text(data.atjauninasanas_datums);

        $("#slud_ID").val(id);
        $("#sludinajums_saglabat_admin").text("Saglabāt");

        toggleFormFields3();
        renderGallery(data.atteli);
        $("#atteluGalerijaContainerAdmin").show();
        $(".nomainitAttelusRinda").show();
        toggleSludAtteluSelect();
      }
    );
  });

  $("#sludinajumaFormaAdmin").submit((e) => {
    e.preventDefault();

    const formData = new FormData($("#sludinajumaFormaAdmin")[0]);

    const isEdit = $("#slud_ID").val() !== "";
    const url = isEdit
      ? "../assets/database/sludinajumi_edit.php"
      : "../assets/database/sludinajumi_add.php";

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
          $("#sludFormPazinojumsAdmin").text(response);
          return;
        }

        if (response.includes("veiksmīgs")) {
          paradit_pazinojumu(response);
          fetchSludinajumi();
          return;
        }

        $(".modal").hide();
        $("#sludinajumaFormaAdmin").trigger("reset");
        $("#sludFormPazinojumsAdmin").text("");
        fetchSludinajumi();
      },
      error: function () {
        alert("Neizdevās nosūtīt sludinājumu!");
      },
    });
  });

  function toggleSludAtteluSelect() {
    const val = $("#sludNomainitAtteliSelectAdmin").val();
    if (val === "ja") {
      $(".nomainit-slud-atteli").show();
    } else {
      $(".nomainit-slud-atteli").hide();
    }
  }

  function renderGallery(images) {
    galleryImages = images;
    currentImageIndex = 0;

    const gallery = $("#atteluGalerijaAdmin");
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
      $("#modalImageAdmin").attr("src", "data:image/jpeg;base64," + src);
      $("#imageModalAdmin").css("display", "flex");
    }
  }

  $("#atteluGalerijaAdmin").on("click", "img", function () {
    currentImageIndex = index;
    showModalImage();
  });

  $("#prevImageAdmin").on("click", () => {
    currentImageIndex =
      (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
    showModalImage();
  });

  $("#nextImageAdmin").on("click", () => {
    currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
    showModalImage();
  });

  $(document).on("keydown", function (e) {
    if ($("#imageModalAdmin").is(":visible")) {
      if (e.key === "ArrowLeft") {
        $("#prevImageAdmin").click();
      } else if (e.key === "ArrowRight") {
        $("#nextImageAdmin").click();
      } else if (e.key === "Escape") {
        $("#imageModalAdmin").hide();
      }
    }
  });

  $(document).on("click", ".sludinajums-delete", (e) => {
    if (confirm("Vai esat pārliecināts, ka vēlaties dzēst šo sludinājumu?")) {
      const element = $(e.currentTarget).closest("tr");
      const id = $(element).attr("slud_ID");
      const tabula = $(element).data("veids");
      // console.log(id)
      $.post(
        "./database/sludinajumi_delete.php",
        { id, tabula },
        (response) => {
          fetchSludinajumi();
        }
      );
    }
  });

  // #endregion

  //#region Pieteikumi
  function fetchPieteikumi() {
    $.ajax({
      url: "./database/pieteikumi_list.php",
      type: "GET",
      success: function (response) {
        const pieteikumi = JSON.parse(response);
        const rowsPerPage = 15;
        let currentPage = 1;

        function renderTable(page) {
          let template = "";
          const start = (page - 1) * rowsPerPage;
          const end = start + rowsPerPage;
          const pageItems = pieteikumi.slice(start, end);

          pageItems.forEach((pieteikums) => {
            const klaseSarkans =
              pieteikums.statuss === "Atteikums" ? "sarkans" : "";
            const klaseZals =
              pieteikums.statuss === "Mājoklis ir iegādāts" ? "zals" : "";
            template += `
                          <tr piet_ID="${pieteikums.id}">
                              <td>${pieteikums.id}</td>
                              <td>${pieteikums.majokla_tips}</td>
                              <td>${pieteikums.adrese}</td>
                              <td>${pieteikums.cena}</td>
                              <td>${pieteikums.epasts}</td>
                              <td class='${klaseSarkans} ${klaseZals}'>${pieteikums.statuss}</td>
                              <td>${pieteikums.izveidosanas_datums}</td>
                              <td class="items">
                                  <a class="piet-item editBtn"> <i class="fa fa-edit"></i> </a>    
                                  <a class="piet-delete deleteBtn"> <i class="fa fa-trash"></i> </a>
                              </td>
                          </tr>
                      `;
          });

          $("#pieteikumi").html(template);
        }

        function renderPagination() {
          const totalPages = Math.ceil(pieteikumi.length / rowsPerPage);
          let buttons = "";

          for (let i = 1; i <= totalPages; i++) {
            buttons += `<button class="${
              i === currentPage ? "active" : ""
            }" data-page="${i}">${i}</button>`;
          }

          $("#pagination-piet").html(buttons);
        }

        $("#pagination-piet").on("click", "button", function () {
          currentPage = parseInt($(this).attr("data-page"));
          renderTable(currentPage);
          renderPagination();
        });

        renderTable(currentPage);
        renderPagination();
      },
      error: function () {
        alert("Neizdevas ieladet datus");
      },
    });
  }

  $(document).on("click", ".piet-item", (e) => {
    $(".modalPieteikumi").css("display", "flex");

    const element = $(e.currentTarget).closest("tr");
    const id = $(element).attr("piet_ID");

    $.post(
      "../assets/database/liet_pieteikumi_single.php",
      { id },
      (response) => {
        const pieteikums = JSON.parse(response);
        $("#pietlietotajsAdmin").val(pieteikums.epasts);
        $("#pietMajoklaTipsAdmin").val(pieteikums.majokla_tips);
        $("#pietAdreseAdmin").val(pieteikums.adrese);
        $("#pietCenaAdmin").val(pieteikums.cena);
        $("#pietStatussAdmin").val(pieteikums.statuss);
        $("#piet_ID").val(pieteikums.id);
        $("#pietDatumsAdmin").val(pieteikums.izveidosanas_datums);
        $("#ipAdresePiet").text(pieteikums.ip_adrese);
        $("#atjauninasanasDatumsPiet").text(pieteikums.atjauninasanas_datums);
      }
    );
  });

  $(document).on("click", ".piet-delete", (e) => {
    if (confirm("Vai esat pārliecināts, ka vēlaties dzēst šo pieteikumu?")) {
      const element = $(e.currentTarget).closest("tr");
      const id = $(element).attr("piet_ID");
      $.post("../assets/database/pieteikumi_delete.php", { id }, (response) => {
        fetchPieteikumi();
      });
    }
  });

  $("#pieteikumaFormaAdmin").submit((e) => {
    e.preventDefault();
    const postData = {
      statuss: $("#pietStatussAdmin").val(),
      id: $("#piet_ID").val(),
    };

    url = "../assets/database/liet_pieteikumi_edit.php";
    $.post(url, postData, (response) => {
      if (response.includes("veiksmīgi")) {
        paradit_pazinojumu(response);
        fetchPieteikumi();
        return;
      }

      $(".modalPieteikumi").hide();
      $("#pieteikumaFormaAdmin").trigger("reset");
      fetchPieteikumi();
    });
  });

  // #endregion

  //#region Īres ieraksti
  function fetchIresIeraksti() {
    $.ajax({
      url: "./database/ires_ieraksti_list.php",
      type: "GET",
      success: function (response) {
        const ieraksti = JSON.parse(response);
        const rowsPerPage = 15;
        let currentPage = 1;

        function renderTable(page) {
          let template = "";
          const start = (page - 1) * rowsPerPage;
          const end = start + rowsPerPage;
          const pageItems = ieraksti.slice(start, end);

          pageItems.forEach((ieraksts) => {
            template += `
              <tr ires_ID="${ieraksts.id}">
                <td>${ieraksts.id}</td>
                <td>${ieraksts.majokla_tips}</td>
                <td>${ieraksts.adrese}</td>
                <td>${ieraksts.epasts}</td>
                <td>${ieraksts.registresanas_datums}</td>
                <td>${ieraksts.izrakstisanas_datums}</td>
                <td>${ieraksts.cena}</td>
                <td>${ieraksts.izveidosanas_datums}</td>
                <td class="items">
                  <a class="ires-item editBtn"> <i class="fa fa-edit"></i> </a>    
                  <a class="ires-delete deleteBtn"> <i class="fa fa-trash"></i> </a>
                </td>
              </tr>`;
          });

          $("#iresIeraksti").html(template);
        }

        function renderPagination() {
          const totalPages = Math.ceil(ieraksti.length / rowsPerPage);
          let buttons = "";

          for (let i = 1; i <= totalPages; i++) {
            buttons += `<button class="${
              i === currentPage ? "active" : ""
            }" data-page="${i}">${i}</button>`;
          }

          $("#pagination-ires").html(buttons);
        }

        $("#pagination-ires").on("click", "button", function () {
          currentPage = parseInt($(this).attr("data-page"));
          renderTable(currentPage);
          renderPagination();
        });

        renderTable(currentPage);
        renderPagination();
      },
      error: function () {
        alert("Neizdevas ieladet datus");
      },
    });
  }

  $(document).on("click", ".ires-item", (e) => {
    $(".modalIresIeraksts").css("display", "flex");

    const element = $(e.currentTarget).closest("tr");
    const id = $(element).attr("ires_ID");

    $.post("./database/ires_ieraksts_single.php", { id }, (response) => {
      const iresIerakstsTabula = JSON.parse(response);
      $("#majoklaTipsIres").val(iresIerakstsTabula.majokla_tips);
      $("#adreseIres").val(iresIerakstsTabula.adrese);
      $("#lietotajsIres").val(iresIerakstsTabula.epasts);
      $("#iznomatsNo").val(iresIerakstsTabula.iznomatsNo);
      $("#iznomatsLidz").val(iresIerakstsTabula.iznomatsLidz);
      $("#cenaIret").val(iresIerakstsTabula.cena);
      $("#atjauninasanasDatumsIres").text(
        iresIerakstsTabula.atjauninasanas_datums
      );
      $("#ipAdreseIres").text(iresIerakstsTabula.ip_adrese);
      $("#ires_ID").val(iresIerakstsTabula.id);
    });
  });

  // #endregion
});
