"use strict";
var disAvancar = false;
function janela(pagina, TW, TH) {
    window.open(pagina, 'cliente', "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=yes" + ",width=" + TW + ",height=" + TH);
}
function FiltraCampo(codigo) {
    var s = "";
    tam = codigo.length;
    for (i = 0; i < tam; i++) {
        if (codigo.substring(i, i + 1) == "0" ||
            codigo.substring(i, i + 1) == "1" ||
            codigo.substring(i, i + 1) == "2" ||
            codigo.substring(i, i + 1) == "3" ||
            codigo.substring(i, i + 1) == "4" ||
            codigo.substring(i, i + 1) == "5" ||
            codigo.substring(i, i + 1) == "6" ||
            codigo.substring(i, i + 1) == "7" ||
            codigo.substring(i, i + 1) == "8" ||
            codigo.substring(i, i + 1) == "9")
            s = s + codigo.substring(i, i + 1);
    }
    return s;
}
function DvCnpjOk(e) {
    var dv = false;
    controle = "";
    s = FiltraCampo(e.value);
    tam = s.length;
    if (tam == 14) {
        dv_cnpj = s.substring(tam - 2, tam);
        for (i = 0; i < 2; i++) {
            soma = 0;
            for (j = 0; j < 12; j++)
                soma += s.substring(j, j + 1) * ((11 + i - j) % 8 + 2);
            if (i == 1)
                soma += digito * 2;
            digito = 11 - soma % 11;
            if (digito > 9)
                digito = 0;
            controle += digito;
        }
        if (controle == dv_cnpj)
            dv = true;
    }
    if (!dv && tam > 0) {
        mensagem = "           Erro de digita��o:\n";
        mensagem += "          ===============\n\n";
        mensagem += " O CNPJ: " + e.value + " n�o existe!!\n";
        mensagem += " CONTROLE " + controle + "\n";
        alert(mensagem);
    }
    return dv;
}
function FormataCnpj(e) {
    var s = "";
    var r = "";
    s = FiltraCampo(e.value);
    tam = s.length;
    r = s.substring(0, 2) + "." + s.substring(2, 5) + "." + s.substring(5, 8);
    r += "/" + s.substring(8, 12) + "-" + s.substring(12, 14);
    if (tam < 3)
        s = r.substring(0, tam);
    else if (tam < 6)
        s = r.substring(0, tam + 1);
    else if (tam < 9)
        s = r.substring(0, tam + 2);
    else if (tam < 13)
        s = r.substring(0, tam + 3);
    else
        s = r.substring(0, tam + 4);
    e.value = s;
    return s;
}
function DvMod11Ok(e) {
    var res = false;
    s = FiltraCampo(e.value);
    tam = s.length;
    soma = 0;
    dv = s.substring(tam - 1, tam);
    for (i = 0; i < tam - 1; i++) {
        soma += s.substring(i, i + 1) * (11 - tam + i);
    }
    digito = soma % 11;
    if (digito == 10)
        digito = 0;
    if (digito == dv)
        res = true;
    else {
        mensagem = "           Erro de digita\347\343o:\n";
        mensagem += "          ===============\n\n";
        mensagem += "   D�gito verificador incorreto!!\n";
        alert(mensagem);
    }
    return res;
}
function DvCpfOk(e) {
    var dv = false;
    controle = "";
    s = FiltraCampo(e.value);
    tam = s.length;
    if (tam == 11) {
        dv_cpf = s.substring(tam - 2, tam);
        for (i = 0; i < 2; i++) {
            soma = 0;
            for (j = 0; j < 9; j++)
                soma += s.substring(j, j + 1) * (10 + i - j);
            if (i == 1)
                soma += digito * 2;
            digito = (soma * 10) % 11;
            if (digito == 10)
                digito = 0;
            controle += digito;
        }
        if (controle == dv_cpf)
            dv = true;
    }
    if (!dv && tam > 0) {
        mensagem = "           Erro de digita��o:\n";
        mensagem += "          ===============\n\n";
        mensagem += " O CPF: " + e.value + " n�o existe!!\n";
        alert(mensagem);
    }
    return dv;
}
function FormataCpf(e) {
    var s = "";
    s = FiltraCampo(e.value);
    tam = s.length;
    r = s.substring(0, 3) + "." + s.substring(3, 6) + "." + s.substring(6, 9);
    r += "-" + s.substring(9, 11);
    if (tam < 4)
        s = r.substring(0, tam);
    else if (tam < 7)
        s = r.substring(0, tam + 1);
    else if (tam < 10)
        s = r.substring(0, tam + 2);
    else
        s = r.substring(0, tam + 3);
    e.value = s;
    return s;
}
function DvProcessoOk(e) {
    var dv = false;
    s = FiltraCampo(e.value);
    tam = s.length;
    if (tam == 15 || tam == 17) {
        num = s.substring(0, tam - 2);
        for (i = 0; i < 2; i++) {
            soma = 0;
            mult = num.length + 1;
            for (k = 0; k < num.length; k++)
                soma += num.substring(k, k + 1) * (mult - k);
            mod11 = 11 - (soma % 11);
            if (mod11 < 10)
                dv_proc = "0" + mod11;
            else
                dv_proc = mod11 + "";
            dv_proc = dv_proc.substring(1, 2);
            num += dv_proc;
        }
        if (num == s)
            dv = true;
    }
    if (!dv && tam > 0) {
        mensagem = "           Erro de digita��o:\n";
        mensagem += "          ===============\n\n";
        mensagem += " O Processo: " + e.value + " n�o existe!!\n";
        alert(mensagem);
    }
    return dv;
}
function FormataProcesso(e) {
    var s = "";
    s = FiltraCampo(e.value);
    tam = s.length;
    if (tam == 17 && s.substring(0, 5) == "02000" &&
        s.substring(11, 13) == "20")
        ano_dig = 4;
    else
        ano_dig = 2;
    r = s.substring(0, 5) + "." + s.substring(5, 11) + "/";
    r += s.substring(11, 11 + ano_dig) + "-" + s.substring(11 + ano_dig, 13 + ano_dig);
    if (tam < 6)
        s = r.substring(0, tam);
    else if (tam < 12)
        s = r.substring(0, tam + 1);
    else if (tam < 12 + ano_dig)
        s = r.substring(0, tam + 2);
    else
        s = r.substring(0, tam + 3);
    e.value = s;
    return s;
}
function DataOk(e) {
    var dv = false;
    s = FiltraCampo(e.value);
    tam = s.length;
    if (tam == 8) {
        dia = parseInt(s.substring(0, 2), 10);
        mes = parseInt(s.substring(2, 4), 10);
        ano = parseInt(s.substring(4, 8), 10);
        if (dia > 0 && dia < 32 &&
            mes > 0 && mes < 13 &&
            ano > 1900 && ano < 2100)
            dv = true;
    }
    if (!dv && tam > 0) {
        mensagem = "           Erro de digita��o:\n";
        mensagem += "          ===============\n\n";
        mensagem += " A data tem a seguinte formata��o: DD/MM/AAAA!!\n";
        mensagem += "        onde DD � Dia do m�s,\n";
        mensagem += "             MM � o n�mero do m�s,e \n";
        mensagem += "             AAAA � o Ano com 4 d�gitos.\n\n";
        mensagem += " Exemplo: 15/04/2001\n";
        alert(mensagem);
        e.focus();
    }
    return dv;
}
function FormataData(e) {
    var s = "";
    s = FiltraCampo(e.value);
    tam = s.length;
    r = s.substring(0, 2) + "/" + s.substring(2, 4) + "/";
    r += s.substring(4, 8);
    if (tam < 3)
        s = r.substring(0, tam);
    else if (tam < 5)
        s = r.substring(0, tam + 1);
    else
        s = r.substring(0, tam + 2);
    e.value = s;
    return s;
}
function Avancar(e) {
    e.form.Avancar.disabled = true;
}
function CepOk(e) {
    var dv = false;
    s = FiltraCampo(e.value);
    tam = s.length;
    if (tam == 8) {
        dv = true;
    }
    if (tam > 0 && tam < 8) {
        mensagem = "           Erro de digita��o:\n";
        mensagem += "          ===============\n\n";
        mensagem += " O Cep: " + e.value + " n�o existe!!\n\n\n";
        mensagem += " Use o seguinte formato: ddddd-ddd\n\n";
        mensagem += " Exemplo: 70800-200\n";
        alert(mensagem);
        e.focus();
    }
    //Avancar(e);
    return dv;
}
function FormataCep(e) {
    var s = "";
    s = FiltraCampo(e.value);
    tam = s.length;
    r = s.substring(0, 5) + "-" + s.substring(5, 8);
    if (tam < 6)
        s = r.substring(0, tam);
    else
        s = r.substring(0, tam + 1);
    e.value = s;
    return s;
}
function FormataFoneFax(e) {
    var s = "";
    var res = "";
    s = FiltraCampo(e.value);
    while (s.substring(0, 1) == "0") {
        s1 = s.substring(1, s.length);
        s = s1;
    }
    if (s.length == 14 || s.length == 12)
        s = s.substring(s.length - 10, s.length);
    if (s.length == 13 || s.length == 11)
        s = s.substring(s.length - 9, s.length);
    res = s.substring(s.length - 4, s.length);
    if (s.length > 4 && s.length < 9)
        res = s.substring(0, s.length - 4) + "-" + res;
    if (s.length > 8)
        res = "(0XX" + s.substring(0, 2) + ") " +
            s.substring(2, s.length - 4) + "-" + res;
    e.value = res;
    return res;
}
function Varredura(e, varNotNull) {
}
function FormataCpfCnpj(e) {
    var s = "";
    s = FiltraCampo(e.value);
    if (s.length == 11) {
        if (!DvCpfOk(e)) {
            for (var sch = 0; sch < e.form.length; sch++) {
                if (e.form.elements[sch].name.toLowerCase() == "bt_validar")
                    e.form.elements[sch].disabled = true;
            }
        }
        else {
            for (var sch = 0; sch < e.form.length; sch++) {
                if (e.form.elements[sch].name.toLowerCase() == "bt_validar")
                    e.form.elements[sch].disabled = false;
            }
        }
        s = FormataCpf(e);
        return s;
    }
    else if (s.length == 14) {
        DvCnpjOk(e);
        s = FormataCnpj(e);
        return s;
    }
    else {
        for (var sch = 0; sch < e.form.length; sch++) {
            if (e.form.elements[sch].name.toLowerCase() == "bt_validar")
                e.form.elements[sch].disabled = true;
        }
        mensagem = "           Erro de digita\347\343o:\n";
        mensagem += "          ===============\n\n";
        mensagem += " O CNPJ ou CPF: " + e.value + " n\343o existe!!\n";
        alert(mensagem);
        return s = '';
    }
}
function submeter(e) {
    if (document.formulario.bt_submit.value.length < 3)
        document.formulario.bt_submit.disabled = true;
    else
        document.formulario.bt_submit.disabled = false;
}
function informaCpfCnpj(e) {
    var s = "";
    var alt = true;
    s = FiltraCampo(e.value);
    if (s.length == 11) {
        if (DvCpfOk(e))
            document.formulario.bt_submit.disabled = false;
        else {
            document.formulario.bt_submit.disabled = true;
            document.formulario.NUM_PESSOA_NOME_CNPJ_CPF.focus();
        }
        s = FormataCpf(e);
    }
    else if (s.length == 14) {
        if (DvCnpjOk(e))
            document.formulario.bt_submit.disabled = false;
        else {
            document.formulario.bt_submit.disabled = true;
            document.formulario.NUM_PESSOA_NOME_CNPJ_CPF.focus();
        }
        s = FormataCnpj(e);
    }
    else if (e.value.length > 2) {
        document.formulario.bt_submit.disabled = false;
    }
    else {
        if (document.formulario.bt_submit.disabled == false)
            document.formulario.bt_submit.disabled = true;
    }
    return s;
}
function FormataCpfCnpjCadastro(e) {
    var s = "";
    s = FiltraCampo(e.value);
    if (s.length == 11) {
        DvCpfOk(e);
        s = FormataCpf(e);
    }
    else if (s.length == 14) {
        DvCnpjOk(e);
        s = FormataCnpj(e);
    }
    else if (s.length == 8) {
        DvMod11Ok(e);
        e.value = s.substring(0, 7) + "-" + s.substring(7, 8);
        s = e.value;
    }
    else if (s.length > 7 || s.length <= 0) {
        mensagem = "           Erro de digita\347\343o:\n";
        mensagem += "          ===============\n\n";
        mensagem += " N�mero de cadastro: " + e.value + " n\343o existe!!\n";
        alert(mensagem);
    }
    return s;
}
function IntOk(e, min, max) {
    var v = parseInt(e.value, 10);
    var v2 = parseFloat(e.value);
    if ((v < min || v > max) && e.value.length > 0) {
        mensagem = "           Erro de digita\347\343o:\n";
        mensagem += "          ===============\n\n";
        mensagem += e.value + " n�o � um valor entre ";
        mensagem += min + " e " + max + "!!\n";
        alert(mensagem);
        return false;
    }
    if (v != v2) {
        mensagem = "           Erro de digita\347\343o:\n";
        mensagem += "          ===============\n\n";
        mensagem += e.value + " n�o � � um n�mero inteiro!!\n ";
        alert(mensagem);
        return false;
    }
    if (e.value.length > 0 && v >= min && v <= max && v == v2) {
        e.value = parseInt(e.value, 10);
        return true;
    }
    else {
        mensagem = "           Erro de digita\347\343o:\n";
        mensagem += "          ===============\n\n";
        mensagem += e.value + " n�o � � um n�mero!!\n ";
        alert(mensagem);
        return false;
    }
    e.value = parseInt(e.value, 10);
}
function FloatOk(e, min, max) {
    var v = parseFloat(e.value);
    if ((v < min || v > max) && e.value.length > 0) {
        mensagem = "           Erro de digita\347\343o:\n";
        mensagem += "          ===============\n\n";
        mensagem += e.value + " n�o � um valor entre ";
        mensagem += min + " e " + max + "!!\n";
        alert(mensagem);
        return false;
    }
    if (e.value.length > 0 && v >= min && v <= max) {
        e.value = parseFloat(e.value);
        return true;
    }
    else {
        mensagem = "           Erro de digita\347\343o:\n";
        mensagem += "          ===============\n\n";
        mensagem += e.value + " n�o � � um n�mero!!\n ";
        alert(mensagem);
        return false;
    }
}
function PreencherOk(e, min) {
    var j = 0;
    for (var i = 0; i < e.value.length; i++) {
        var c = e.value.charAt(i);
        if ((c != ' ') && (c != '\n') && (c != '\t'))
            j++;
    }
    if (j < min) {
        mensagem = "           Erro de digita\347\343o:\n";
        mensagem += "          ===============\n\n";
        mensagem += e.value + " n�o cont�m o n�mero m�nimo de  ";
        mensagem += "carateres!!\n";
        alert(mensagem);
        return false;
    }
    else
        return true;
}
function VerificarForm(f) {
    for (var i = 0; i < f.length; i++) {
        var e = f.elements[i];
        var sbmt = true;
        if (((e.type == "text") || (e.type == "textarea")) && !e.optional) {
            if ((e.value == null) || (e.value == "") || (!PreencherOk(e, 1))) {
                sbmt = false;
            }
        }
        if ((e.type == "select-one") && !e.optional
            && e.selectedIndex == 0) {
            sbmt = false;
        }
    }
    alert("Type: " + e.type);
    if (!sbmt) {
        mensagem = "           Falta preencher dados\n";
        mensagem += "          ===============\n\n";
        mensagem += "Este formul�rio tem campos obrigat�rios que";
        mensagem += " n�o foram preenchidos!!\n";
        alert(mensagem);
        return false;
    }
    else
        return true;
}
function getRefToDiv(divID) {
    if (document.getElementById) {
        return document.getElementById(divID);
    }
    if (document.layers) {
        return document.layers[divID];
    }
    if (document.all) {
        return document.all[divID];
    }
    if (document[divID]) {
        return document[divID];
    }
    return false;
}
function ShowItem(e) {
    e.style.visibility = "visible";
}
function HideItem(e) {
    e.style.visibility = "hidden";
}
function EsconderSelects(f) {
    for (i = 0; i < f.length; i++) {
        if (f.elements[i].type == "select-one") {
            var nome = f.elements[i].name;
            HideItem(f.elements[i]);
            ShowItem(getRefToDiv(nome + "div"));
        }
    }
}
function MostrarSelects(f) {
    for (i = 0; i < f.length; i++) {
        if (f.elements[i].type == "select-one") {
            var nome = f.elements[i].name;
            HideItem(getRefToDiv(nome + "div"));
            ShowItem(f.elements[i]);
        }
    }
}
