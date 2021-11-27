(function($){
    $("#submitTax").click(function(){
        $("#taxError").hide();
        $("#results").hide();
        let netSalary;
        let grossSalary;
        let pention;
        let incomeTax;
        grossSalary = $("#monthlySalary").val();
        if(!grossSalary || grossSalary == 0){

            $("#taxError").html("<small>Please Enter a  valid salary.</small>");
            $("#taxError").show();
            return;
        }
        if(grossSalary > 500){
            pention = 300;
            incomeTax = 0.15 * grossSalary;
            netSalary = grossSalary - pention;- incomeTax;
        }
        $("#pention").text(pention);
        $("#incomeTax").text(incomeTax);
        $("#netSalary").text(netSalary);
        $("#grossSalary").text(grossSalary);
        $("#result").show();
    })
})(jQuery)