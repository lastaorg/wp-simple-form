/* jQuery(document).ready(function() {
    (function($) {
        $("#submitTax").click(function() {
            $("#taxError").hide();
            $("#results").hide();
            let netSalary;
            let grossSalary;
            let pention;
            let incomeTax;
            let rate;
            let deducatable;
            let pention_rate = .07;

            grossSalary = Number.parseFloat($("#monthlySalary").val());

            if (!grossSalary || grossSalary == 0) {

                $("#taxError").html("<small>Please Enter a  valid salary.</small>");
                $("#taxError").show();
                return;
            }

            if (grossSalary <= 600) {
                rate = 0;
                deducatable = 0;
            } else if (600 < grossSalary && grossSalary <= 1650) {
                rate = 0.1;
                deducatable = 60;
            } else if (1650 < grossSalary && grossSalary <= 3200) {
                rate = 0.15;
                deducatable = 142.5;
            } else if (3200 < grossSalary && grossSalary <= 5250) {
                rate = 0.20;
                deducatable = 302.50;
            } else if (5250 < grossSalary && grossSalary <= 7800) {
                rate = 0.25;
                deducatable = 565.00;
            } else if (7800 < grossSalary && grossSalary <= 10900) {
                rate = 0.30;
                deducatable = 955.00;
                break;
            } else if (10900 < grossSalary) {
                rate = 0.35;
                deducatable = 1500.00;
            } else {
                console.log("noting matxched", grossSalary, typeof grossSalary);
            }

            console.log("rate", rate, deducatable);

            pention = pention_rate * grossSalary;
            incomeTax = (rate * grossSalary) - deducatable;
            netSalary = grossSalary - pention - incomeTax;

            $("#pention").text(pention);
            $("#incomeTax").text(incomeTax);
            $("#netSalary").text(netSalary);
            $("#grossSalary").text(grossSalary);
            $("#results").show();
        })
    })(jQuery)
}); */