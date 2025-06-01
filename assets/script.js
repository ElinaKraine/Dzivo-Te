//#region Modals logs
let modalBtns = document.querySelectorAll("[data-target]");
let closeModal = document.querySelectorAll(".close-modal");

modalBtns.forEach(function (btn) {
  btn.addEventListener("click", function () {
    const target = document.querySelector(btn.dataset.target);
    if (target) target.classList.add("modal-active");
  });
});

closeModal.forEach(function (btn) {
  btn.addEventListener("click", function () {
    const target = document.querySelector(btn.dataset.target);
    if (target) target.classList.remove("modal-active");
  });
});
//#endregion

//#region Vēstules nosutīšana
if (window.history.replaceState) {
  window.history.replaceState(null, null, window.location.href);
}

x = () => {
  let alert = document.getElementById("pazinojums");
  alert.style.display = "none";
};
//#endregion

//#region Funkcija, lai pārslēgt paroles redzamību
function parslegtParolesRedzamibu(ikona, parole) {
  if (!ikona || !parole) return;

  ikona.addEventListener("click", function () {
    if (parole.type === "password") {
      parole.type = "text";
      ikona.classList.add("fa-eye-slash");
      ikona.classList.remove("fa-eye");
    } else {
      parole.type = "password";
      ikona.classList.add("fa-eye");
      ikona.classList.remove("fa-eye-slash");
    }
  });
}
//#endregion

//#region Attēlu skaita ierobežojums
function validateAtteluInput(input) {
  const maxFiles = 10;
  const maxSize = 100 * 1024;

  if (input.files.length === 0 || input.files.length > maxFiles) {
    alert("Jāizvēlas vismaz 1 un ne vairāk kā 10 attēli.");
    input.value = "";
    return false;
  }

  for (const file of input.files) {
    if (file.size > maxSize) {
      alert(`Attēls "${file.name}" pārsniedz 100KB limitu.`);
      input.value = "";
      return false;
    }
  }

  return true;
}
//#endregion

//#region Funkcija sludinājumu pievienošanai vai dzēšanai no saglabātajiem
function pievienot_dzest_saglabatu() {
  document
    .querySelectorAll(".sirds[data-id][data-veids][data-tips]")
    .forEach((poga) => {
      poga.addEventListener("click", function (e) {
        e.preventDefault();
        if (poga.classList.contains("processing")) return;

        poga.classList.add("processing");

        const sludinajumaId = poga.dataset.id;
        const veids = poga.dataset.veids;
        const tips = poga.dataset.tips;
        const ikona = poga.querySelector("i");
        const irSaglabats = ikona.classList.contains("fa-solid");

        const url = irSaglabats
          ? "./assets/database/dzest_saglabatu.php"
          : "./assets/database/pievienot_saglabatiem.php";

        fetch(url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `id_sludinajums=${sludinajumaId}&veids=${veids}&tips=${tips}`,
        })
          .then((res) => res.json())
          .then((data) => {
            if (data.success) {
              if (irSaglabats) {
                ikona.classList.remove("fa-solid");
                ikona.classList.add("fa-regular");
                poga.classList.remove("sirdsSarkans");
              } else {
                ikona.classList.remove("fa-regular");
                ikona.classList.add("fa-solid");
                poga.classList.add("sirdsSarkans");
              }
            } else {
              if (data.message === "unauthorized") {
                window.location.href = "./login.php";
              } else {
                alert(data.message || "Darbība neizdevās.");
              }
            }
          })
          .catch(() => {
            alert("Neizdevās veikt darbību.");
          })
          .finally(() => {
            poga.classList.remove("processing");
          });
      });
    });
}
//#endregion

//#region Animācija
function animacija(pirmaisElements, masivsArElementiem, sekcija) {
  if (!sekcija || !(sekcija instanceof Element)) {
    console.error(
      "Nav atrasta novērojamā sekcija vai sekcija nav derīgs elements!"
    );
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          if (pirmaisElements) {
            pirmaisElements.classList.add("redzams");
          }

          masivsArElementiem.forEach((elements, indeks) => {
            setTimeout(() => {
              elements.classList.add("redzams");
            }, indeks * 300);
          });

          observer.disconnect();
        }
      });
    },
    { threshold: 0.1 }
  );

  observer.observe(sekcija);
}
//#endregion

document.addEventListener("DOMContentLoaded", function () {
  // Pārslegt paroles
  const icon1 = document.getElementById("parslegtParole1");
  const icon2 = document.getElementById("parslegtParole2");
  const icon3 = document.getElementById("parslegtParole3");
  const parole1 = document.getElementById("parole1");
  const parole2 = document.getElementById("parole2");
  const parole3 = document.getElementById("parole3");

  if (icon1 && parole1) parslegtParolesRedzamibu(icon1, parole1);
  if (icon2 && parole2) parslegtParolesRedzamibu(icon2, parole2);
  if (icon3 && parole3) parslegtParolesRedzamibu(icon3, parole3);

  // Navigācija
  const menuButton = document.querySelector(".menu");

  if (menuButton) {
    menuButton.addEventListener("click", function () {
      const menuWrapper = document.querySelector(".menu-wrapper");
      if (!menuWrapper) return;

      menuWrapper.classList.toggle("active");

      const icon = menuButton.querySelector("i");
      if (!icon) return;

      if (icon.classList.contains("fa-bars")) {
        icon.classList.remove("fa-bars");
        icon.classList.add("fa-xmark");
      } else {
        icon.classList.remove("fa-xmark");
        icon.classList.add("fa-bars");
      }
    });
  }

  // Animācijas
  const parInfo = document.querySelector(".parInfo");
  const parInfoAttelas = document.querySelectorAll(
    ".pirmaAttela, .otraAttela, .tresaAttela"
  );
  const parMajasLapu = document.querySelector(".parMajaslapu");

  const lietotajaDarbibasTeksts = document.querySelector(
    ".lietotajaDarbibas h1"
  );
  const darbibas = document.querySelectorAll(".darbiba");
  const lietotajaDarbibas = document.querySelector(".lietotajaDarbibas");

  const piedavajumiTeksts = document.querySelector(".piedavajumi h1");
  const mazasKastites = document.querySelectorAll(".piedavajumi .mazaKaste");
  const piedavajumi = document.querySelector(".piedavajumi");

  const komentarijuSekcija = document.querySelector(".komentariji");
  const komentariji = document.querySelectorAll(".komentarijs");

  if (parMajasLapu) animacija(parInfo, parInfoAttelas, parMajasLapu);
  if (lietotajaDarbibas)
    animacija(lietotajaDarbibasTeksts, darbibas, lietotajaDarbibas);
  if (piedavajumi) animacija(piedavajumiTeksts, mazasKastites, piedavajumi);
  if (komentarijuSekcija) animacija("", komentariji, komentarijuSekcija);

  // Animācija ar statistikai
  const kastesArStatistikai = document.querySelectorAll(".kasteInfo h1");
  const galvenaStatistika = document.querySelector(".galvenaStatistika");
  let irSaskaitits = false;

  function irSkatloga(element) {
    const rect = element.getBoundingClientRect();
    return (
      rect.top >= 0 &&
      rect.left >= 0 &&
      rect.bottom <=
        (window.innerHeight || document.documentElement.clientHeight) &&
      rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
  }

  function startCounting() {
    if (irSaskaitits) return;
    irSaskaitits = true;

    kastesArStatistikai.forEach((counter) => {
      const target = +counter.innerText;
      let startCount = target > 25 ? target - 25 : 1;
      let count = startCount;
      const increment = Math.ceil((target - startCount) / 1000);

      const updateCounter = () => {
        count += increment;
        if (count < target) {
          counter.innerText = count;
          setTimeout(updateCounter, 50);
        } else {
          counter.innerText = target;
        }
      };

      updateCounter();
    });
  }

  window.addEventListener("scroll", () => {
    if (galvenaStatistika && irSkatloga(galvenaStatistika)) {
      startCounting();
    }
  });

  if (galvenaStatistika && irSkatloga(galvenaStatistika)) {
    startCounting();
  }

  // funkcijas
  initializeFilterToggle();
  pievienot_dzest_saglabatu();
  initGlobalGalleryNavigation();
  initBodyClickHandlers();
});

//#region Filtras pogas
function initializeFilterToggle() {
  document.querySelectorAll(".filter-poga").forEach((button) => {
    button.addEventListener("click", function () {
      const dropdown = this.nextElementSibling;
      const icon = this.querySelector("i");

      document
        .querySelectorAll(
          ".cenuDiapozons, .istabasDiapozons, .platibasDiapozons, .staviDiapozons"
        )
        .forEach((div) => {
          if (div !== dropdown) {
            div.style.display = "none";
            const otherButton = div.previousElementSibling;
            const otherIcon = otherButton.querySelector("i");
            otherIcon.classList.remove("fa-chevron-up");
            otherIcon.classList.add("fa-chevron-down");
          }
        });

      if (dropdown.style.display === "block") {
        dropdown.style.display = "none";
        icon.classList.remove("fa-chevron-up");
        icon.classList.add("fa-chevron-down");
      } else {
        dropdown.style.display = "block";
        icon.classList.remove("fa-chevron-down");
        icon.classList.add("fa-chevron-up");
      }
    });
  });
}
//#endregion

//#region Pirk un Iret pogas Mājam:
const atlasitaPoga = document.querySelector(".atlasits");
const neAtlasitaPoga = document.querySelector(".neAtlasits");
const contentContainer = document.getElementById("contentContainer");
let currentPage = "";

function parslegtPogasKlases(clickedButton) {
  atlasitaPoga.classList.toggle("atlasits", clickedButton === atlasitaPoga);
  atlasitaPoga.classList.toggle("neAtlasits", clickedButton !== atlasitaPoga);
  neAtlasitaPoga.classList.toggle("atlasits", clickedButton === neAtlasitaPoga);
  neAtlasitaPoga.classList.toggle(
    "neAtlasits",
    clickedButton !== neAtlasitaPoga
  );
}

function loadContent(page) {
  currentPage = page;
  const xhr = new XMLHttpRequest();
  xhr.open("GET", `assets/${page}.php`, true);
  xhr.onload = function () {
    if (this.status === 200) {
      contentContainer.innerHTML = this.responseText;
      initializeFilterToggle();

      if (
        typeof initMajasAsinhronieSkripti === "function" &&
        page === "majasPardosanai"
      ) {
        initMajasAsinhronieSkripti();
      }
    } else {
      console.error("Failed to load content");
    }
  };
  contentContainer.innerHTML = "";
  xhr.send();
}

if (atlasitaPoga && neAtlasitaPoga && contentContainer) {
  atlasitaPoga.addEventListener("click", function () {
    parslegtPogasKlases(this);
    currentPage = "";
    loadContent("majasPardosanai");
  });

  neAtlasitaPoga.addEventListener("click", function () {
    parslegtPogasKlases(this);
    currentPage = "";
    loadContent("majasIresanai");
  });

  document.addEventListener("DOMContentLoaded", function () {
    if (atlasitaPoga.classList.contains("atlasits")) {
      loadContent("majasPardosanai");
    } else {
      loadContent("majasIresanai");
    }
  });
}
//#endregion

//#region Pirk un Iret pogas Dzīvokļiem:
const atlasitaPogaDziv = document.querySelector(".atlasitsDziv");
const neAtlasitaPogaDziv = document.querySelector(".neAtlasitsDziv");
const contentContainerDzivokli = document.getElementById(
  "contentContainerDzivokli"
);
let currentPageDziv = "";

function parslegtPogasKlasesDzivokli(clickedButton) {
  atlasitaPogaDziv.classList.toggle(
    "atlasitsDziv",
    clickedButton === atlasitaPogaDziv
  );
  atlasitaPogaDziv.classList.toggle(
    "neAtlasitsDziv",
    clickedButton !== atlasitaPogaDziv
  );
  neAtlasitaPogaDziv.classList.toggle(
    "atlasitsDziv",
    clickedButton === neAtlasitaPogaDziv
  );
  neAtlasitaPogaDziv.classList.toggle(
    "neAtlasitsDziv",
    clickedButton !== neAtlasitaPogaDziv
  );
}

function loadContentDziv(page) {
  currentPageDziv = page;
  const xhr = new XMLHttpRequest();
  xhr.open("GET", `assets/${page}.php`, true);
  xhr.onload = function () {
    if (this.status === 200) {
      contentContainerDzivokli.innerHTML = this.responseText;
      initializeFilterToggle();

      if (page === "dzivokliPardosanai") {
        const script = document.createElement("script");
        script.src = "assets/script-asinhrons-dziv-pirkt.js";
        script.onload = function () {
          if (typeof initDzivokliAsinhronieSkripti === "function") {
            initDzivokliAsinhronieSkripti();
          }
        };
        document.body.appendChild(script);
      }

      if (page === "dzivokliIresanai") {
        const script = document.createElement("script");
        script.src = "assets/script-asinhrons-dziv-iret.js";
        document.body.appendChild(script);
      }
    } else {
      console.error("Failed to load content");
    }
  };
  contentContainerDzivokli.innerHTML = "";
  xhr.send();
}

if (atlasitaPogaDziv && neAtlasitaPogaDziv && contentContainerDzivokli) {
  atlasitaPogaDziv.addEventListener("click", function () {
    parslegtPogasKlasesDzivokli(this);
    currentPageDziv = "";
    loadContentDziv("dzivokliPardosanai");
  });

  neAtlasitaPogaDziv.addEventListener("click", function () {
    parslegtPogasKlasesDzivokli(this);
    currentPageDziv = "";
    loadContentDziv("dzivokliIresanai");
  });

  document.addEventListener("DOMContentLoaded", function () {
    if (atlasitaPogaDziv.classList.contains("atlasitsDziv")) {
      loadContentDziv("dzivokliPardosanai");
    } else {
      loadContentDziv("dzivokliIresanai");
    }
  });
}
//#endregion

//#region Galereja
let globalGalleryImages = [];
let currentGalleryIndex = 0;

function initAtteluGalerija(containerSelector) {
  const container = document.querySelector(containerSelector);
  if (!container) return;

  const imgElements = container.querySelectorAll("img");
  globalGalleryImages = [];
  currentGalleryIndex = 0;

  imgElements.forEach((img, index) => {
    globalGalleryImages.push(img.src);
    img.addEventListener("click", () => {
      currentGalleryIndex = index;
      showGlobalModalImage();
    });
  });
}

function showGlobalModalImage() {
  const modal = document.getElementById("imageModal");
  const imgEl = document.getElementById("modalImage");
  if (!modal || !imgEl) return;

  imgEl.src = globalGalleryImages[currentGalleryIndex];
  modal.style.display = "flex";
  modal.classList.add("modal-active");
}

function initGlobalGalleryNavigation() {
  const prev = document.getElementById("prevImage");
  const next = document.getElementById("nextImage");
  const modal = document.getElementById("imageModal");
  const close = modal?.querySelector(".close-modal");

  if (prev && next && modal) {
    prev.onclick = () => {
      currentGalleryIndex =
        (currentGalleryIndex - 1 + globalGalleryImages.length) %
        globalGalleryImages.length;
      showGlobalModalImage();
    };

    next.onclick = () => {
      currentGalleryIndex =
        (currentGalleryIndex + 1) % globalGalleryImages.length;
      showGlobalModalImage();
    };
  }

  if (close) {
    close.addEventListener("click", () => {
      modal.style.display = "none";
    });
  }

  document.addEventListener("keydown", (e) => {
    if (!modal || modal.style.display !== "flex") return;

    if (e.key === "ArrowLeft") {
      prev?.click();
    } else if (e.key === "ArrowRight") {
      next?.click();
    } else if (e.key === "Escape") {
      modal.style.display = "none";
    }
  });
}
//#endregion

//#region index.php atvert sludinājumu atseviškaja lapa
function initBodyClickHandlers() {
  document.body.addEventListener("click", function (e) {
    const target = e.target;

    // Открытие модального окна
    if (target.matches("[data-target]")) {
      const modal = document.querySelector(target.dataset.target);
      if (modal) modal.classList.add("modal-active");
    }

    // Закрытие модального окна
    if (
      target.classList.contains("close-modal") ||
      target.closest(".close-modal")
    ) {
      const el = target.closest(".close-modal");
      const modal = document.querySelector(el.dataset.target);
      if (modal) modal.classList.remove("modal-active");
    }

    // Переход на страницу объявления из index.php
    const ad = target.closest(".sludinajumsIndex");
    if (ad) {
      const id = ad.getAttribute("data-id");
      const tips = ad.getAttribute("data-tips");
      if (!id || !tips) return;

      if (tips === "Maja") {
        window.location.href = `maja_pirkt.php?id=${id}`;
      } else if (tips === "Dzivoklis") {
        window.location.href = `dzivoklis_pirkt.php?id=${id}`;
      }
    }
  });
}

//#endregion
