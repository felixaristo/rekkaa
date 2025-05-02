function setLabelBjsRate(bpjsRate) {
    let jkkrate = 0;

    $(".penghasilan_jp_label").append(` (2 %)`);
    $(".penghasilan_jht_label").append(` (3.7 %)`);
    $(".pengurangan_jamkes_label").append(` (1 %)`);
    bpjsRate.forEach((bpjs) => {
        if (bpjs.bpjsrate_code == "JamKes") {
            $(".penghasilan_jamkes_label").append(` (${bpjs.bpjsrate_rate} %)`);
        }
        if (bpjs.bpjsrate_code == "JKK") {
            if(jkkrate == 0)
            {
                jkkrate = bpjs.bpjsrate_rate;
                $(".penghasilan_jkk_label .rate_label").html(`(${jkkrate} %)`);
            }
            $("ul.dropdown-menu-jamkes").append(`<li><a class="dropdown-item" data-rate="${bpjs.bpjsrate_rate}" href="javascript:void(0);">${bpjs.bpjsrate_rate}</a></li>`);
        }
        if (bpjs.bpjsrate_code == "JKM") {
            $(".penghasilan_jkm_label").append(` (${bpjs.bpjsrate_rate} %)`);
        }
        // if (bpjs.bpjsrate_code == "JHT") {
            // $(".penghasilan_jht_label").append(` (${bpjs.bpjsrate_rate} %)`);
            
        // }
        // if (bpjs.bpjsrate_code == "JP") {
            // $(".penghasilan_jp_label").append(` (${bpjs.bpjsrate_rate} %)`);
            
        // }

        // if (bpjs.bpjsrate_code == "JamKesMin") {
            // $(".pengurangan_jamkes_label").append(` (${bpjs.bpjsrate_rate} %)`);
            
        // }
        if (bpjs.bpjsrate_code == "JHTMin") {
            $(".pengurangan_jht_label").append(` (${bpjs.bpjsrate_rate} %)`);
        }
        if (bpjs.bpjsrate_code == "JPMin") {
            $(".pengurangan_jp_label").append(` (${bpjs.bpjsrate_rate} %)`);
        }
    });
}
function getBpjsRate(bpjsRate, code = null, id = null) {
    let bpjsrate = bpjsRate.filter((bpjs) => {
        if(id) {
            return (bpjs.bpjsrate_code == code && bpjs.bpjsrate_id == id) ? bpjs : null;
        } else {
            return bpjs.bpjsrate_code == code ? bpjs : null;
        }
    });
    if (bpjsrate.length == 1) {
        return bpjsrate[0];
    } else {
        return bpjsrate;
    }
}
// // console.log(getTarif21(89000000));
function getTarif21(tarif21, value = 0) {
    return tarif21.filter((t21) => {
        return t21.tarif21_startincome <= value;
    });
}

function perhitunganTarifPPH21(
    tarif21,
    metode = "GROSS",
    kepemilikanNpwp,
    perhitunganTotalPKP = 0,
    tarif21Nonnpwp
) {
    let tarif21Rates = getTarif21(tarif21, perhitunganTotalPKP);
    // // console.log("perhitunganTotalPKP", perhitunganTotalPKP);
    // // console.log("tarif21Rates", tarif21Rates);
    let totalPKP = perhitunganTotalPKP;
    let totalPPHTerutangSetahun = 0;
    let rateNonNPWP =
        kepemilikanNpwp == "NO-NPWP" ? tarif21Nonnpwp.tarifnonnpwp_rate : 100;

    if (metode == "GROSS" || metode == "NETT") {
        tarif21Rates.forEach((rate) => {
            if (rate.tarif21_endincome > totalPKP) {
                totalPPHTerutangSetahun +=
                    ((totalPKP * rate.tarif21_rate) / 100) *
                    (rateNonNPWP / 100);
                    // // console.log('totalPPHTerutangSetahun', totalPPHTerutangSetahun);
            } else {
                totalPPHTerutangSetahun +=
                    ((rate.tarif21_endincome * rate.tarif21_rate) / 100) *
                    (rateNonNPWP / 100);
                totalPKP -= rate.tarif21_endincome;
                // // console.log('totalPPHTerutangSetahun', totalPPHTerutangSetahun);
                // // console.log('totalPKP', totalPKP);
            }
        });
    } else {
        // GROSS UP
        tarif21Rates.forEach((rate, idx) => {
            let rangeRate = 0;
            let newRate = 0;
            if (idx > 0) {
                let prevRate = tarif21Rates[idx - 1];
                rangeRate =
                    ((prevRate.tarif21_endincome -
                        prevRate.tarif21_startincome) *
                        prevRate.tarif21_rate) /
                    100;
                newRate = prevRate.tarif21_endincome - rangeRate;
            }

            if (idx == tarif21Rates.length - 1) {
                // // console.log("rangeRate " + idx, rangeRate);
                totalPPHTerutangSetahun +=
                    (((totalPKP - newRate) *
                        rate.tarif21_rate) /
                        (100 - rate.tarif21_rate) +
                        rangeRate) *
                    (rateNonNPWP / 100);
            }
        });
    }
    // // console.log("totalPPHTerutangSetahun", totalPPHTerutangSetahun);
    return Math.floor(totalPPHTerutangSetahun);
}

function perhitunganTarifPPH21TER(
    tarif21,
    metode = "GROSS",
    kepemilikanNpwp,
    perhitunganTotalPKP = 0,
    tarif21Nonnpwp
) {
    let totalPKP = perhitunganTotalPKP;
    let tarif21Rates = getTarif21(tarif21, totalPKP);
    
    let preveRate = 0;
    let totalTarif21 = 0;
    let i=0;
    tarif21Rates.forEach((rate) => {
        if(i == 0) {
            preveRate = 0;
        }
        let maxIncome = rate.tarif21_endincome;
        if(tarif21Rates[i+1] == undefined) {
            maxIncome = totalPKP - preveRate;
        } else {
            maxIncome -= preveRate;
        }
        // endIncome;
        preveRate += maxIncome;
        totalTarif21 += ((maxIncome * rate.tarif21_rate) / 100);
        i++;
        
    });
    return Math.floor(totalTarif21);
}

// function perhitunganTotalPPH21(
//     bpjsRate,
//     ptkp,
//     tunjangan = [],
//     metode = "GROSS",
//     npwp = "NPWP"
// ) {
//     bpjsRateJamKes = getBpjsRate(bpjsRate, "JamKes");
//     bpjsRateJamKesMin = getBpjsRate(bpjsRate, "JamKesMin");
//     bpjsRateJKK = getBpjsRate(bpjsRate, "JKK");
//     bpjsRateJKM = getBpjsRate(bpjsRate, "JKM");
//     bpjsRateJHT = getBpjsRate(bpjsRate, "JHT");
//     bpjsRateJHTMin = getBpjsRate(bpjsRate, "JHTMin");
//     bpjsRateJP = getBpjsRate(bpjsRate, "JP");
//     bpjsRateJPMin = getBpjsRate(bpjsRate, "JPMin");

//     let perhitunganTotalNetoSetahun = penghasilanNetoPerbulan * 12;

//     // let ptkpRate = parseInt(ptkp.ptkp_rate);
//     // if (ptkpRate > perhitunganTotalNetoSetahun.getNumber()) {
//     //     ptkpRate = perhitunganTotalNetoSetahun.getNumber();
//     // }
//     // let pkp = perhitunganTotalNetoSetahun - ptkpRate;

//     // let totalPPHTerutangSetahun = perhitunganTarifPPH21(
//     //     tarif21,
//     //     metodePajak,
//     //     kepemilikanNpwp,
//     //     perhitunganTotalPKP.getNumber(),
//     //     tarif21Nonnpwp
//     // );
//     return {
//         bpjsRateJamKes: bpjsRateJamKes,
//         bpjsRateJamKesMin: bpjsRateJamKesMin,
//         bpjsRateJKK: bpjsRateJKK,
//         bpjsRateJKM: bpjsRateJKM,
//         bpjsRateJHT: bpjsRateJHT,
//         bpjsRateJHTMin: bpjsRateJHTMin,
//         bpjsRateJP: bpjsRateJP,
//         bpjsRateJPMin: bpjsRateJPMin,
//         // penghasilanNetoPertahun: penghasilanNetoPerbulan.getNumber() * 12,
//         // ptkpRate: ptkpRate,
//         // pkp: pkp,
//     };

//     let metodePajak = $("input[name=tunjangan_pajak]:checked").val();
//     // // // console.log('metodePajak', metodePajak);
//     let kepemilikanNpwp = $("#kepemilikan_npwp").val();
//     // let totalPPHTerutangSetahun = perhitunganTarifPPH21(
//     //     tarif21,
//     //     metodePajak,
//     //     kepemilikanNpwp,
//     //     perhitunganTotalPKP.getNumber(),
//     //     tarif21Nonnpwp
//     // );
//     perhitunganTotalPPHTerutangSetahun.set(totalPPHTerutangSetahun);
//     perhitunganTotalPPHTerutangPerbulan.set(totalPPHTerutangSetahun / 12);
// }
