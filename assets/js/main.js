var puniCasovi;
fetch("./assets/js/puniCasovi.json")
    .then(response => response.json())
    .then(data => {
        puniCasovi = data;
    });
//puniCasovi = JSON.parse(puniCasovi);
let niz;
let currentPeriod = 0;

//izvlacenje pocetnih i zavrsnih vremena trazenog casa "period" za prvu i drugu smenu
function getPeriodStartEndTimes(period) {
    var jsonData = niz[period - 1][1];

    var start1 = jsonData.start1;
    var end1 = jsonData.end1;
    var start2 = jsonData.start2;
    var end2 = jsonData.end2;

    var firstStart = new Date();
    var firstEnd = new Date();
    var secondStart = new Date();
    var secondEnd = new Date();

    firstStart.setHours(start1.split(':')[0]);
    firstStart.setMinutes(start1.split(':')[1])
    firstStart.setSeconds(0);

    firstEnd.setHours(end1.split(':')[0]);
    firstEnd.setMinutes(end1.split(':')[1])
    firstEnd.setSeconds(0);

    secondStart.setHours(start2.split(':')[0]);
    secondStart.setMinutes(start2.split(':')[1])
    secondStart.setSeconds(0);

    secondEnd.setHours(end2.split(':')[0]);
    secondEnd.setMinutes(end2.split(':')[1])
    secondEnd.setSeconds(0);

    return [firstStart, firstEnd, secondStart, secondEnd];
}

//izracunavanje trenutnog casa na osnovu datog trenutnog vremena now
function getCurrentPeriod(now) {

    try {
        niz = Object.entries(puniCasovi); //niz pocetnih i zavrsnih vremena casova

        //listanje kroz niz pocetnih i zavrsnih vremena casova
        for (let i = 0; i < niz.length; i++) {

            //uzimanje pocetnih i zavrsnih vremena casova u prvoj i drugoj smeni
            var timeArray = getPeriodStartEndTimes(parseInt(niz[i][0]));
            var firstStart = timeArray[0];
            var firstEnd = timeArray[1];
            var secondStart = timeArray[2];
            var secondEnd = timeArray[3];

            if ((now > firstStart && now <= firstEnd) || (now > secondStart && now <= secondEnd)) { // da li je cas
                return niz[i][0]; // redni broj casa
            } else if ((now > firstEnd && now < getPeriodStartEndTimes((parseInt(niz[i][0]) % 7) + 1)[0]) || (now > secondEnd && now < getPeriodStartEndTimes((parseInt(niz[i][0]) % 7) + 1)[2])) { //da li je odmor izmedju bilo koja dva casa od 1. do 6.
                return [niz[i][0], "Odmor"]; //odmor je, nakon casa niz[i][0]
            } else if ((now > firstEnd && now < secondEnd && niz[i][0] == 7) || (now > secondEnd && niz[i][0] == 7)) { //edgecase, da li je odmor posle 7. casa
                return [niz[i][0], "Odmor"]; //odmor je, nakon casa niz[i][0]
            }
        }

    } catch (error) {
        console.log(error);
    }

}

//vreme do sledece vremenske etape (kraj casa, kraj odmora) na osnovu trenutnog casa, i stanja odmora (jeste/nije)
function getTimeToNextPeriod(period, isOdmor = false) {
    period = parseInt(period);
    var remainingTime;
    var date = new Date(); // trenutno vreme
    //vremenski offset zbog sata u skoli
    date.setMinutes(date.getMinutes() + 2);
    date.setSeconds(date.getSeconds() + 5);
    var timeArray;

    if (isOdmor) { //ako je odmor
        if (period == 7) { //ako je sedmi cas prosao, a odmor je
            //ispisuje da je u pitanju kraj smene
            document.getElementById("current-period-display").innerHTML = "Смена";
            document.getElementById("remaining-time-display").innerHTML = "00:00";
            return;
        } else { //ako nije 7 cas prosao, a odmor je

            timeArray = getPeriodStartEndTimes((period % 7) + 1); //uzima pocetno vreme sledeceg casa u obe smene

            var firstStart = new Date();
            var secondStart = new Date();

            firstStart = timeArray[0];
            secondStart = timeArray[2];

            if (date > firstStart && date < secondStart) { //da li je proslo pocetno vreme casa u prvoj smeni al ne u drugoj
                // console.log("if");
                remainingTime = secondStart.getTime() - date.getTime(); //do pocetka casa u drugoj smeni
            } else {
                // console.log("else");
                remainingTime = firstStart.getTime() - date.getTime(); //do pocetka casa u prvoj smeni
            }

            //formatiranje preostalog vremena u m:s
            var diffMins = parseInt(remainingTime / (1000 * 60));
            var diffSecs = parseInt((remainingTime - (diffMins * (1000 * 60))) / 1000);


            document.getElementById("current-period-display").innerHTML = "Одмор";
            document.getElementById("remaining-time-display").innerHTML = (diffMins < 10 ? '0' : '') + diffMins + ":" + (diffSecs < 10 ? '0' : '') + diffSecs;
        }
    } else { //ako je cas u toku
        timeArray = getPeriodStartEndTimes(period);

        var firstStart = new Date();
        var secondStart = new Date();

        firstEnd = timeArray[1];
        secondEnd = timeArray[3];

        if (date > firstEnd && date <= secondEnd) { //ako je prosao kraj casa u prvoj smeni a ne u drugoj
            remainingTime = secondEnd.getTime() - date.getTime(); //kraj casa u drugoj smeni
        } else {
            remainingTime = firstEnd.getTime() - date.getTime(); //kraj casa u prvoj
        }

        //formatiranje preostalog vremena u m:s
        var diffMins = parseInt(remainingTime / (1000 * 60));
        var diffSecs = parseInt((remainingTime - (diffMins * (1000 * 60))) / 1000);




        document.getElementById("current-period-display").innerHTML = period + ". час";
        document.getElementById("remaining-time-display").innerHTML = (diffMins < 10 ? '0' : '') + diffMins + ":" + (diffSecs < 10 ? '0' : '') + diffSecs;
    }
}

//dobijanje trenutnog vremena i datuma
function getCurrentTime() {
    var date = new Date(); // trenutno vreme
    //vremenski offset zbog skolskog sata
    date.setMinutes(date.getMinutes() + 2);
    date.setSeconds(date.getSeconds() + 5);

    //recnik koji pretvara indeks dana iz kratkog u dugi format
    var daniUNedelji = ["Недеља", "Понедељак", "Уторак", "Среда", "Четвртак", "Петак", "Субота"];

    //dodavanje "leading zeroes" na trenutno kratko vreme
    var time = (date.getHours() < 10 ? '0' : '') + date.getHours() + ":" + (date.getMinutes() < 10 ? '0' : '') + date.getMinutes() + ":" + (date.getSeconds() < 10 ? '0' : '') + date.getSeconds();

    document.getElementById("time-display").innerHTML = time;

    //formatiranje dugog vremena tj trenutnog datuma
    var longDate = daniUNedelji[date.getDay()] + ", " + date.getDate() + "." + (date.getMonth() + 1) + "." + date.getFullYear() + ".";
    document.getElementById("date-display").innerHTML = longDate;
    var period = getCurrentPeriod(date);

    //provera koji je cas/odmor u toku, i preostalog vremena
    try {
        if (period[1] !== undefined) { //ako je odmor
            getTimeToNextPeriod(period[0], period[1]);
        } else { //ako je cas
            getTimeToNextPeriod(period[0]);
        }
    } catch (error) {
        console.log(error);
    }

    //ako je vikend, sakriva deo za pracenje trenutnog casa i preostalog vremena
    if (date.getDay() == 0 || date.getDay() == 6) {
        document.getElementById("current-period-container").style = "display: none";
    } else {
        document.getElementById("current-period-container").style = "display: flex";
    }

}


function refreshPage() {
    document.location.reload(true); //refreshuje stranicu i brise cache
}

//inicijalno dobijanje vremena
getCurrentTime()
setInterval(getCurrentTime, 1000); //ponavljanje na svaki sekund

setTimeout(refreshPage, 3600000); // refreshovanje stranice na svakih sat vremena zbog mogucnosti postavljanja izmena na daljinu