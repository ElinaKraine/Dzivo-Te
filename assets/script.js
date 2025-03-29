// Modals logs
let modalBtns = document.querySelectorAll("[data-target]");
let closeModal = document.querySelectorAll(".close-modal");

modalBtns.forEach(function (btn) {
  btn.addEventListener("click", function () {
    document.querySelector(btn.dataset.target).classList.add("modal-active");
  });
});

closeModal.forEach(function (btn) {
  btn.addEventListener("click", function () {
    document.querySelector(btn.dataset.target).classList.remove("modal-active");
  });
});

// Vēstules nosutīšana
if (window.history.replaceState) {
  window.history.replaceState(null, null, window.location.href);
}

x = () => {
  let alert = document.getElementById("pazinojums");
  alert.style.display = "none";
};

// Funkcija, lai pārslēgt paroles redzamību
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

// Animācija
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

  initializeFilterToggle();

  if (atlasitaPoga.classList.contains("atlasits")) {
    loadContent("majasPardosanai");
  } else {
    loadContent("majasIresanai");
  }
});

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

// Pirk un Iret pogas:
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
  if (currentPage === page) return;
  currentPage = page;

  const xhr = new XMLHttpRequest();
  xhr.open("GET", `assets/${page}.php`, true);
  xhr.onload = function () {
    if (this.status === 200) {
      contentContainer.innerHTML = this.responseText;
      initializeFilterToggle();
    } else {
      console.error("Failed to load content");
    }
  };
  contentContainer.innerHTML = "";
  xhr.send();
}

atlasitaPoga.addEventListener("click", function () {
  parslegtPogasKlases(this);
  loadContent("majasPardosanai");
});

neAtlasitaPoga.addEventListener("click", function () {
  parslegtPogasKlases(this);
  loadContent("majasIresanai");
});
