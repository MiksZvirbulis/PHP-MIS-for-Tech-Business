var keycode = "BH89-EZ67-DX32-GE92";

function findAddress(SearchTerm, Element){
    $.getJSON("http://services.postcodeanywhere.co.uk/CapturePlus/Interactive/Find/v2.10/json3.ws?callback=?",
    {
        Key: keycode,
        SearchTerm: SearchTerm.value,
        SearchFor: "PostalCodes",
        Country: "GBR",
        LanguagePreference: "EN"
    },
    function (data) {
        if(data.Items.length == 1 && typeof(data.Items[0].Error) != "undefined"){
            alert(data.Items[0].Description);
        }else{
            if(data.Items.length == 0){
                alert("Sorry, there were no results");
            }else{
               $("#" + Element + "_addressSelection option").remove();
               $("#" + Element + "_addressSelection").append($("<option></option>").attr("disabled", "disabled").attr("selected", "selected").text("Select Address"));
               for(var i = 0, l = data.Items.length; i < l; i++){
                $("#" + Element + "_addressSelection").append($("<option></option>").attr("value", data.Items[i].Id).text(data.Items[i].Text));
            }
        }
    }
});
}

function retrieveAddress(Id, Element){
    $.getJSON("http://services.postcodeanywhere.co.uk/CapturePlus/Interactive/Retrieve/v2.10/json3.ws?callback=?",
    {
        Key: keycode,
        Id: Id.value
    },
    function (data) {
        if(data.Items.length == 1 && typeof(data.Items[0].Error) != "undefined"){
            alert(data.Items[0].Description);
        }else{
            if (data.Items.length == 0){
                alert("Sorry, there were no results");
            }else{
                // PUT YOUR CODE HERE
                //FYI: The output is a JS object (e.g. data.Items[0].Id), the keys being:
                //Id
                //DomesticId
                //Language
                //LanguageAlternatives
                //Department
                //Company
                //SubBuilding
                //BuildingNumber
                //BuildingName
                //SecondaryStreet
                //Street
                //Block
                //Neighbourhood
                //District
                //City
                //Line1
                //Line2
                //Line3
                //Line4
                //Line5
                //AdminAreaName
                //AdminAreaCode
                //Province
                //ProvinceName
                //ProvinceCode
                //PostalCode
                //CountryName
                //CountryIso2
                //CountryIso3
                //CountryIsoNumber
                //SortingNumber1
                //SortingNumber2
                //Barcode
                //POBoxNumber
                //Label
                //Type
                //DataLevel
                if(data.Items[0].Company == ""){
                 $("input#" + Element + "_line1").val(data.Items[0].Line1);
                 $("input#" + Element + "_line2").val(data.Items[0].Line2);
             }else{
               $("input#" + Element + "_line1").val(data.Items[0].Company);
               $("input#" + Element + "_line2").val(data.Items[0].BuildingNumber + " " + data.Items[0].Street);
           }
           $("input#" + Element + "_line3").val(data.Items[0].Line3);
           $("input#" + Element + "_line4").val(data.Items[0].City);
           $("input#" + Element + "_postcode").val(data.Items[0].PostalCode);
       }
   }
});
}