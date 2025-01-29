// Funkcija, lai pārslēgt paroles redzamību
function parslegtParolesRedzamibu(ikona, parole) {
    if (!ikona || !parole) return;
    
    ikona.addEventListener('click', function () {
        if (parole.type === 'password') {
            parole.type = 'text';
            ikona.classList.add('fa-eye-slash');
            ikona.classList.remove('fa-eye');
        } else {
            parole.type = 'password';
            ikona.classList.add('fa-eye');
            ikona.classList.remove('fa-eye-slash');
        }
    });
}

// Animācija
function animacija(pirmaisElements, masivsArElementiem, sekcija) {
    if (!sekcija || !(sekcija instanceof Element)) {
        console.error("Nav atrasta novērojamā sekcija vai sekcija nav derīgs elements!");
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

document.addEventListener('DOMContentLoaded', function () {
    // Pārslegt paroles
    const icon1 = document.getElementById('parslegtParole1');
    const icon2 = document.getElementById('parslegtParole2');
    const icon3 = document.getElementById('parslegtParole3');
    const parole1 = document.getElementById('parole1');
    const parole2 = document.getElementById('parole2');
    const parole3 = document.getElementById('parole3');

    if (icon1 && parole1) parslegtParolesRedzamibu(icon1, parole1);
    if (icon2 && parole2) parslegtParolesRedzamibu(icon2, parole2);
    if (icon3 && parole3) parslegtParolesRedzamibu(icon3, parole3);

    // Animācijas
    const parInfo = document.querySelector(".parInfo");
    const parInfoAttelas = document.querySelectorAll(".pirmaAttela, .otraAttela, .tresaAttela");
    const parMajasLapu = document.querySelector(".parMajaslapu");

    const lietotajaDarbibasTeksts = document.querySelector(".lietotajaDarbibas h1");
    const darbibas = document.querySelectorAll(".darbiba");
    const lietotajaDarbibas = document.querySelector(".lietotajaDarbibas");

    const piedavajumiTeksts = document.querySelector(".piedavajumi h1");
    const mazasKastites = document.querySelectorAll(".piedavajumi .mazaKaste");
    const piedavajumi = document.querySelector(".piedavajumi");

    const komentarijuSekcija = document.querySelector(".komentariji");
    const komentariji = document.querySelectorAll(".komentarijs");

    if (parMajasLapu) animacija(parInfo, parInfoAttelas, parMajasLapu);
    if (lietotajaDarbibas) animacija(lietotajaDarbibasTeksts, darbibas, lietotajaDarbibas);
    if (piedavajumi) animacija(piedavajumiTeksts, mazasKastites, piedavajumi);
    if (komentarijuSekcija) animacija("", komentariji, komentarijuSekcija);

    //Animācija ar statistikai
    const kastesArStatistikai = document.querySelectorAll('.kasteInfo h1');
    const galvenaStatistika = document.querySelector('.galvenaStatistika');
    let irSaskaitits = false;

    function irSkatloga(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    function startCounting() {
        if (irSaskaitits) return;
        irSaskaitits = true;

        kastesArStatistikai.forEach(counter => {
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

    window.addEventListener('scroll', () => {
        if (irSkatloga(galvenaStatistika)) {
            startCounting();
        }
    });

    if (irSkatloga(galvenaStatistika)) {
        startCounting();
    }
});