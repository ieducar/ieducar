"use strict";
var Lb0 = 0;
var JL1 = 0;
var b0b = 0;
var J0 = 0;
var LL0 = 0;
var TbT = 0;
var b1b = 0;
var bJ = 0;
var TLL = 0;
var JJ0 = 0;
var JT1 = 0;
var LJ = 0;
var L0 = [];
var _Tab = 0;
var T1L = false;
var b0J = 0;
var Jbb = null;
var s = "";
var TT0 = "";
var TLJ = 2000;
var JTb;
L0JT();
function L0JT() { var a = navigator.userAgent; var n = navigator.appName; var v = navigator.appVersion; JJ0 = v.indexOf("Mac") >= 0; TLL = document.getElementById ? 1 : 0; var TJL = (parseInt(navigator.productSub) >= 20020000) && (navigator.vendor.indexOf("Apple Computer") != -1); if (TJL && navigator.product == "Gecko") {
    b0b = 1;
    bJ = 6;
    return;
} if (n.toLowerCase() == "konqueror") {
    b1b = 1;
    bJ = 1.6;
    return;
} if (a.indexOf("Opera") >= 0) {
    LL0 = 1;
    bJ = parseFloat(a.substring(a.indexOf("Opera") + 6, a.length));
}
else if (n.toLowerCase() == "netscape") {
    if (a.indexOf("rv:") != -1 && a.indexOf("Gecko") != -1 && a.indexOf("Netscape") == -1) {
        b1b = 1;
        bJ = parseFloat(a.substring(a.indexOf("rv:") + 3, a.length));
    }
    else {
        b0b = 1;
        if (a.indexOf("Gecko") != -1 && a.indexOf("Netscape") > a.indexOf("Gecko")) {
            if (a.indexOf("Netscape6") > -1)
                bJ = parseFloat(a.substring(a.indexOf("Netscape") + 10, a.length));
            else if (a.indexOf("Netscape") > -1)
                bJ = parseFloat(a.substring(a.indexOf("Netscape") + 9, a.length));
        }
        else
            bJ = parseFloat(v);
    }
}
else if (document.all ? 1 : 0) {
    Lb0 = 1;
    bJ = parseFloat(a.substring(a.indexOf("MSIE ") + 5, a.length));
} J0 = b0b && bJ < 6; JL1 = Lb0 && bJ >= 5; TbT = LL0 && bJ >= 7; JT1 = Lb0 || TbT; }
function LJ0T() { var LbL = JT1 ? JTb.scrollLeft : pageXOffset; var L0L = JT1 ? JTb.scrollTop : pageYOffset; return [LbL, L0L]; }
function LT0T(TL) { with (TL)
    return [(J0) ? left : parseInt(style.left), (J0) ? top : parseInt(style.top)]; }
function LL0J(TL, J1J, J0J) { with (TL) {
    if (J0) {
        left = J1J;
        top = J0J;
    }
    else {
        style.left = J1J;
        style.top = J0J;
    }
} }
function LTJ0() { if (T1L)
    return; for (var j = 0; j < L0.length; ++j)
    if (L0[j].JbT && L0[j].LbJ) {
        var T1b = LL01("dtabs_b" + j + "div");
        var L10 = LT0T(T1b);
        var bJJ = LJ0T();
        var l = bJJ[0] + L0[j].left;
        var Jb = bJJ[1] + L0[j].top;
        if (L10[0] != l || L10[1] != Jb) {
            var LT0 = (l - L10[0]) / L0[j].J1T;
            var bb0 = (Jb - L10[1]) / L0[j].J1T;
            if (LT0 > -1 && LT0 < 0)
                LT0 = -1;
            else if (LT0 > 0 && LT0 < 1)
                LT0 = 1;
            if (bb0 > -1 && bb0 < 0)
                bb0 = -1;
            else if (bb0 > 0 && bb0 < 1)
                bb0 = 1;
            LL0J(T1b, L10[0] + ((L10[0] != l) ? LT0 : 0), L10[1] + ((L10[1] != Jb) ? bb0 : 0));
        }
    } }
function dtabs_type() { if (tabMode != 0)
    _Tab = 1; }
function Lb10() { JTb = (document.compatMode == "CSS1Compat" && !b1b) ? document.documentElement : document.body; if (!J0 && !(LL0 && bJ < 6))
    for (var j = 0; j < L0.length; ++j)
        if (L0[j].JbT && L0[j].LbJ) {
            window.setInterval("LTJ0()", 20);
            break;
        } if (!_Tab) {
    for (var i = 0; i < L0.length; i++)
        for (var j = 0; j < L0[i].LT.length; j++)
            L0T1(LL01(L0[i].LT[j].link), (L0[i].b1 == j), false);
    if (JL1 && !JJ0) {
        for (var i = 0; i < L0.length; i++)
            for (var j = 0; j < L0[i].LT.length; j++)
                if (L0[i].Jb0 != -1 && L0[i].J01 > 0) {
                    var TL = LL01(L0[i].LT[j].link);
                    if (TL)
                        TL.style.filter = L0bJ(L0[i]);
                }
    }
} b0J = 1; if (Jbb)
    Jbb(); }
function LJ10() { if (window.attachEvent)
    window.attachEvent("onload", Lb10);
else {
    Jbb = (typeof (onload) == 'function') ? onload : null;
    onload = Lb10;
} }
function LT01(TT1, JJL) { return (typeof (TT1) != "undefined" && TT1) ? TT1 : JJL; }
function LJJ0() { JJJ = (babsolute) ? "absolute" : "static"; if (typeof (bmenuHeight) == "undefined")
    bmenuHeight = 0; if (typeof (bseparatorWidth) == "undefined")
    bseparatorWidth = 10; if (bselectedItem < 0)
    bselectedItem = 0; if (typeof (btransition) == "undefined")
    btransition = -1; if (typeof (btransDuration) == "undefined")
    btransDuration = 0; if (typeof (btransOptions) == "undefined")
    btransOptions = ""; }
function dtabs_init() { if (!LJ) {
    dtabs_type();
    LJ10();
} LJJ0(); L0[LJ] = { LT: [], id: "dtabs_b" + LJ, left: bleft, top: btop, JbT: bfloatable, LbJ: babsolute, J1T: (bfloatIterations <= 0) ? 6 : bfloatIterations, width: bmenuWidth, height: bmenuHeight, TJ0: bmenuBorderWidth, T0: bmenuBorderStyle, T1: bmenuBorderColor, JT: bmenuBackColor, JJ: bmenuBackImage, b01: biconAlign, L0J0: bmenuOrientation, LJ1: bbeforeItemSpace, bb1: bafterItemSpace, bbJ: browSpace, b1: bselectedItem, J01: btransDuration, Jb0: btransition, JT0: bselectedSmItem, bbT: bsmBackColor, LJb: bsmBorderColor, TLT: bsmBorderStyle, T0T: bsmBorderWidth, L00J: bsmItemSpacing, L00b: bsmItemPadding }; var Tb1 = L0[LJ]; var L1; var L001 = ""; var cl; var bJT, TTJ, JTT, bTT, TLb, Tbb, JLT, J0T, b0T, LTT; var bLL = [bfontColor[0], LT01(bfontColor[1], ""), LT01(bfontColor[2], "")]; var bbL = [LT01(bfontDecoration[0], "none"), LT01(bfontDecoration[1], "none"), LT01(bfontDecoration[2], "none")]; var b0L = [bitemBackColor[0], LT01(bitemBackColor[1], ""), LT01(bitemBackColor[2], "")]; var bTL = bitemBorderWidth; var LTL = [bitemBorderColor[0], LT01(bitemBorderColor[1], ""), LT01(bitemBorderColor[2], "")]; var b1L = [bitemBorderStyle[0], LT01(bitemBorderStyle[1], ""), LT01(bitemBorderStyle[2], "")]; var LJL = [bitemBackImage[0], LT01(bitemBackImage[1], ""), LT01(bitemBackImage[2], "")]; var LJ00 = biconWidth; var bJL = biconHeight; var LLT; var TTb, JLJ, TJJ; var Lbb; var LTJ = 0; var L1b = 0; var Jb1 = -1; var Lb1 = "_self"; for (var i = 0; (i < bmenuItems.length && typeof (bmenuItems[i]) != "undefined"); i++) {
    cl = 0;
    if (bmenuItems[i][0].charAt(0) == "$") {
        bmenuItems[i][0] = bmenuItems[i][0].substring(1, bmenuItems[i][0].length);
        LTJ++;
    }
    while (bmenuItems[i][0].charAt(cl) == "|")
        cl++;
    L1b = cl;
    if (cl > 0) {
        bmenuItems[i][0] = bmenuItems[i][0].substring(cl, bmenuItems[i][0].length);
        Jb1 = i - 1;
    }
    else
        Jb1 = -1;
    JJ1 = Tb1.LT.length;
    Lbb = LT01(bmenuItems[i][6], "");
    J1 = (Lbb) ? parseInt(Lbb) : -1;
    TTJ = L011("bfontStyle", J1, bfontStyle);
    bJT = L011("bfontColor", J1, bLL);
    JTT = L011("bfontDecoration", J1, bbL);
    bTT = L011("bitemBackColor", J1, b0L);
    TLb = L011("bitemBorderColor", J1, LTL);
    Tbb = L011("bitemBorderWidth", J1, bTL);
    JLT = L011("bitemBorderStyle", J1, b1L);
    TTL = L011("bitemBackImage", J1, LJL);
    LLT = L011("bitemWidth", J1, "");
    b0T = L011("biconW", J1, LJ00);
    LTT = L011("biconH", J1, bJL);
    TTb = L011("bbeforeItemImage", J1, bbeforeItemImage);
    JLJ = L011("bafterItemImage", J1, bafterItemImage);
    TJJ = L011("bitemBackImageSpec", J1, "");
    J0T = LT01(bmenuItems[i][0], "");
    Lb1 = bitemTarget;
    Tb1.LT[JJ1] = { T11: LTJ, L11: L1b, id: Tb1.id + "i" + JJ1, oi: LJ, Tb: JJ1, L0b0: Jb1, text: J0T, link: LT01(bmenuItems[i][1], ""), target: Lb1, T0b: LT01(bmenuItems[i][5], ""), align: L1b ? bsmItemAlign : bitemAlign, L0T0: "middle", cursor: bitemCursor, bJ1: bJT, font: TTJ, bT1: JTT, JT: bTT, JJ: TTL, LJ01: ["", ""], T00: [LT01(bmenuItems[i][2], ""), LT01(bmenuItems[i][3], "")], Tb0: b0T, T10: LTT, bL1: TTb, L0J: bbeforeItemImageW, LJJ: bbeforeItemImageH, LL1: JLJ, LJT: bafterItemImageW, bLT: bafterItemImageH, T1: TLb, TJ0: Tbb, T0: JLT, JJT: bitemSpacing, bTJ: bitemPadding, width: LLT, visible: "visible", L00T: "", L00: TJJ };
} with (L0[LJ]) {
    if (!_Tab) {
        s = "";
        s += "<DIV ID='dtabs_b" + LJ + "div'" + (width ? " width=" + width : "");
        s += " STYLE='position:" + JJJ + ";" + "height:" + height + "px;";
        s += "left: " + left + ";top: " + top + ";z-index:" + TLJ + ";'>";
        s += "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=" + bbJ + (width ? " width=" + width : "");
        s += " STYLE='" + (JT ? "background-color:" + JT : "") + ";margin:0px;" + (JJ ? "background-image:url(" + JJ + ");" : "");
        s += (T0 ? "border-style:" + T0 : '') + ";border-width:" + TJ0 + "px;" + (T1 ? "border-color:" + T1 : "") + ";'>";
    }
    else {
        s = "";
        s += "<DIV ID='dtabs_b" + LJ + "div'" + (width ? " width=" + width : "");
        s += " STYLE='position:" + JJJ + ";" + "height:" + (height + bsmBorderWidth + 1) + "px;";
        s += "left: " + left + ";top: " + top + ";z-index:" + TLJ + "'>";
        s += "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=" + bbJ + (width ? " width=" + width : "");
        s += " STYLE='height:" + (height + bsmBorderWidth + 1) + "px;background-color:" + JT + ";margin:0px;" + (JJ ? "background-image:url(" + JJ + ");" : "");
        s += "border-style:" + T0 + ";border-width:" + TJ0 + "px;border-color:" + T1 + ";'><TR><TD>";
    }
} var L1, J11, JL, J10; var L1J = true; var L101 = ""; var L010 = ""; var b0 = 0; var LL00 = 0; function Lb0T(b00, L01, bT, h) { if (!b00)
    return ""; var q = ""; q += "<TD " + JL + L01 + "td' width=" + bT + " height" + h + " NOWRAP STYLE='padding:0px'>"; q += "<img " + JL + L01 + "' src='" + b00 + "' width=" + bT + " height=" + h + "></TD>"; return q; } function L10J(b00, L01, bT, h) { if (!b00)
    return ""; var q = ""; q += "<TD " + JL + L01 + "td' NOWRAP STYLE='padding:0px; width:" + bT + "px; height:" + h + "px; background-repeat: no-repeat; background-Image:url(" + b00 + ")'>"; q += "<img " + JL + L01 + "blank' src='" + bblankImage + "' width=" + bT + " height=" + h + "></TD>"; return q; } function L0J1(bT, h) { with (L1) {
    if (!T00[0])
        return "";
    var q = "";
    var Lb01 = (b0 ? 1 : 0);
    q += Lb0T((b0 ? T00[1] : T00[0]), 'icon', bT, h);
} return q; } function L0b1(bT) { if (!bT)
    return ""; return "<TD width=" + bT + " NOWRAP STYLE='padding:0px; font-size:1px;'>&nbsp;</TD>"; } function Lb0b() { with (L1) {
    var q = "";
    q += "<TD " + JL + "text' width=100% NOWRAP ALIGN=" + align + " VALIGN=MIDDLE >";
    q += "<FONT " + JL + "font' STYLE='color:" + bJ1[b0] + ";font:" + (font[b0] ? font[b0] : font[0]) + ";text-decoration:" + bT1[b0] + "'>";
    q += ((J0 && link) ? "<a href='" + link + "'>" + text + "</a>" : text) + "</FONT></TD>";
} return q; } function L01b(bT, h) { return "<img src='" + bblankImage + "' width=" + bT + " height=" + h + ">"; } function Lb0J(id, J00) { var displ = ((LL0 && bJ < 7) ? "" : "display:" + (J00 ? "" : "none;")); var LT = L0[LJ]; return "<div " + id + " style='" + (LT.LbJ ? 'position:absolute;left:' + LT.left + ';top:' + (parseInt(LT.top) + parseInt(LT.height)) + 'px;' : '') + "width:" + bmenuWidth + "px; height:" + ((LL0 && bJ < 6) ? "0%;" : bsmHeight + "px;") + displ + " visibility:" + (J00 ? "visible" : "hidden") + ";'><TABLE BORDER=0 width=" + bmenuWidth + " height=" + bsmHeight + " CELLPADDING=" + bsmItemPadding + " CELLSPACING=" + bsmItemSpacing + " STYLE='margin:0px; border:" + bsmBorderStyle + " " + bsmBorderWidth + "px " + bsmBorderColor + "; border-top:none 0px;" + (!bsmBorderBottomDraw ? "border-bottom:none 0px;" : "") + " background-color:" + bsmBackColor + ";'><TR>"; } function LJT0() { return "</TR></TABLE></div>"; } function LJ0b(it) { var q = ""; if (it.text == "-") {
    if (!it.L00)
        q += "<TD " + JL + "' width=" + bseparatorWidth + " style='margin:0px;'>&nbsp;</TD>";
    else
        q += "<TD " + JL + "' width=" + it.width + " style='margin:0px; background-image:url(" + it.L00[L01T(LJ, bL, 0)] + ");background-color:" + it.JT[0] + ";'>" + L01b(it.width, 1) + "</TD>";
}
else {
    with (it) {
        q = "";
        var h = (b10 ? bsmHeight : L0[LJ].height);
        q += "<TD " + JL + "' TITLE='" + T0b + "' " + (text ? (width ? "width=" + width + "px" : "") : "width=1") + (text ? (h ? "height=" + h + "px" : "") : "");
        q += " STYLE='";
        q += "cursor:" + cursor + "; " + (JJ[b0] ? "background-Image:url(" + JJ[b0] + ");" : "");
        q += "border-style:" + T0[b0] + ";border-width:" + TJ0 + "px;" + (!L11 ? "border-bottom: " + TJ0 + "px none" : "") + ";margin:0px; border-color:" + T1[b0] + ";background-color:" + JT[b0] + (J0 ? ";'" : ";display:" + J11 + ";'");
        q += " onMouseOut='L10T(this," + J10 + ",0)' onClick='" + (b10 ? "LTT0" : "L0TJ") + "(" + LJ + "," + bL + ")'>";
    }
    with (L0[LJ]) {
        q += "<TABLE CELLPADDING=" + it.bTJ + " CELLSPACING=" + it.JJT + " BORDER=0 width=100% height=100%><TR>";
        with (it) {
            if (text) {
                if (L11)
                    q += L0b1(LJ1) + ((b01 != "right") ? L0J1(Tb0, T10) + L0b1(LJ1) : "") + Lb0b() + ((b01 == "right") ? L0b1(bb1) + L0J1(Tb0, T10) : "") + L0b1(bb1);
                else
                    q += L10J(bL1[b0], 'bimg', L0J, LJJ) + L0b1(LJ1) + ((b01 != "right") ? L0J1(Tb0, T10) + L0b1(LJ1) : "") + Lb0b() + ((b01 == "right") ? L0b1(bb1) + L0J1(Tb0, T10) : "") + L0b1(bb1) + L10J(LL1[b0], 'aimg', LJT, bLT);
            }
            else
                q += L0J1(Tb0, T10);
        }
    }
    q += "</TR></TABLE>";
    q += "</TD>";
} ; return q; } var J1b = -1; var b10 = false; var JJb = []; var bJb = -1; if (_Tab) {
    s += "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 width=100% STYLE='margin:0px;'><TR>";
    for (var bL = 0; bL < L0[LJ].LT.length; bL++) {
        L1 = L0[LJ].LT[bL];
        JL = "ID='dtabs_b" + LJ + "i" + bL;
        J10 = LJ + "," + bL;
        J11 = (L1.visible == "visible" || L1J) ? "" : "none";
        if (!L1.L11) { }
        else if (!b10) {
            b10 = true;
            TT0 += Lb0J("ID='dtabs_b" + LJ + "i" + (bL - 1) + "sm'", (bL - 1 == L0[LJ].b1));
        }
        if (b10) {
            b0 = (L0[LJ].JT0 == bL) ? 2 : 0;
            TT0 += LJ0b(L1);
        }
        else {
            b0 = (L0[LJ].b1 == bL) ? 2 : 0;
            s += LJ0b(L1);
            bJb++;
            JJb[bJb] = bL;
        }
        ;
        with (L0[LJ]) {
            if (b10)
                if (bL == LT.length - 1 || LT[bL + 1].L11 == 0) {
                    TT0 += LJT0();
                    b10 = false;
                }
        }
    }
    s += "</TR>";
    with (L0[LJ]) {
        if (T0T > 0) {
            s += "<TR>";
            for (var i = 0; i <= bJb; i++) {
                var JbL = ((JJb[i] == b1) ? bbT : LJb);
                var b1T = TLT + " " + T0T + "px " + LJb;
                s += "<TD id='dtabs_b" + LJ + "i" + JJb[i] + "brd' style='padding:0px;font-size:1px;height:" + bsmBorderWidth + "px; border-left:" + b1T + ";border-right:" + b1T + ";background-color:" + JbL + "'>" + L01b(1, 1) + "</TD>";
            }
            s += "</TR>";
        }
    }
    s += "</TABLE>";
    s += "</TD></TR></TABLE></DIV>";
    document.write(s + TT0);
    s = "";
    TT0 = "";
}
else {
    for (var bL = 0; bL < L0[LJ].LT.length; bL++) {
        L1 = L0[LJ].LT[bL];
        JL = "ID='dtabs_b" + LJ + "i" + bL;
        J10 = LJ + "," + bL;
        J11 = (L1.visible == "visible" || L1J) ? "" : "none";
        b0 = (L0[LJ].b1 == bL) ? 2 : 0;
        if (L1.T11 > J1b) {
            s += "<TR " + JL + "tr'><TD>";
            s += "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=" + L1.JJT + " width=100% STYLE='margin:0px;'><TR>";
            J1b = L1.T11;
        }
        if (L1.text == "-") {
            if (!L1.L00)
                s += "<TD " + JL + "' width=" + bseparatorWidth + " style='margin:0px;'>" + L01b(bseparatorWidth, 1) + "</TD>";
            else
                s += "<TD " + JL + "' width=" + L1.width + " style='margin:0px; background-image:url(" + L1.L00[L01T(LJ, bL, 0)] + ");background-color:" + L1.JT[0] + ";'>" + L01b(L1.width, 1) + "</TD>";
        }
        else {
            with (L1) {
                s += "<TD " + JL + "' TITLE='" + T0b + "' " + (text ? (width ? "width=" + width + "px" : "") : "width=1");
                s += " STYLE='";
                s += "cursor:" + cursor + "; background-Image:url(" + JJ[b0] + ");";
                s += "border-style:" + T0[b0] + ";border-width:" + TJ0 + "px;margin:0px; border-color:" + T1[b0] + ";background-color:" + JT[b0] + (J0 ? ";'" : ";display:" + J11 + ";'");
                s += " onMouseOut='L10T(this," + J10 + ",0)' onClick='LTb0(" + LJ + "," + bL + ")'>";
            }
            s += "<TABLE CELLPADDING=" + L1.bTJ + " CELLSPACING=0 BORDER=0 width=100% height=" + L0[LJ].height + "><TR>";
            with (L0[LJ]) {
                with (L1) {
                    if (text)
                        s += L10J(bL1[b0], 'bimg', L0J, LJJ) + L0b1(LJ1) + ((b01 != "right") ? L0J1(Tb0, T10) + L0b1(LJ1) : "") + Lb0b() + ((b01 == "right") ? L0b1(bb1) + L0J1(Tb0, T10) : "") + L0b1(bb1) + L10J(LL1[b0], 'aimg', LJT, bLT);
                    else
                        s += L0J1(Tb0, T10);
                }
            }
            s += "</TR></TABLE>";
            s += "</TD>";
        }
        ;
        if (bL == L0[LJ].LT.length - 1 || L0[LJ].LT[bL + 1].T11 > J1b)
            s += "</TR></TABLE></TD></TR>";
    }
    s += "</TABLE></DIV>";
    document.write(s);
    s = "";
    for (var i = 0; i < L0.length; i++)
        LJb0(i);
} ; if (!LJ && !JJ0 && Lb0)
    TJ1 = LT0J(); LJ++; }
var L1T = [];
var T01 = 0;
function L10b(LLb, excludeInd) { for (var i = 0; i < LLb.length; i++)
    if (i != excludeInd && LLb[i]) {
        T01++;
        L1T[T01] = new Image();
        L1T[T01].src = LLb[i];
    } }
function LJb0(TL1) { var excludeInd = 0; for (var i = 0; i < L0[TL1].LT.length; i++)
    with (L0[TL1].LT[i]) {
        if (L0[TL1].b1 == i)
            excludeInd = 2;
        else
            excludeInd = 0;
        L10b(JJ, excludeInd);
        L10b(T00, excludeInd);
        L10b(bL1, excludeInd);
        L10b(LL1, excludeInd);
        L10b(L00, excludeInd);
    } }
var J1L = [['Blinds'], ['Checkerboard'], ['GradientWipe'], ['Inset'], ['Iris'], ['Pixelate'], ['RadialWipe'], ['RandomBars'], ['RandomDissolve'], ['Slide'], ['Spiral'], ['Stretch'], ['Strips'], ['Wheel'], ['Zigzag']];
function LL0T(JLL, TbL) { if (bJ < 5.5)
    return; var sF = "progid:DXImageTransform.Microsoft." + J1L[JLL - 25] + '(' + btransOptions + ',duration=' + TbL + ')'; return sF; }
function L0bJ(LJ0) { var sF = ""; if (LJ0.Jb0)
    if (LJ0.Jb0 == 24)
        sF += "blendTrans(Duration=" + LJ0.J01 / 1000 + ") ";
    else if (LJ0.Jb0 < 24)
        sF += "revealTrans(Transition=" + LJ0.Jb0 + ",Duration=" + LJ0.J01 / 1000 + ") ";
    else
        sF += LL0T(LJ0.Jb0, LJ0.J01 / 1000); sF += ";"; return sF; }
var JTL = { "nn": 0, "no": 1, "ns": 2, "on": 3, "os": 4, "sn": 5, "so": 6 };
var bTb = -1;
function L01T(LL, bb) { var b1J = 0; with (L0[LL]) {
    var L100 = LT.length;
    var cL = "n", L0T = "n";
    var bT0 = (bb - 1 < 0) ? -1 : bb - 1;
    var bL0 = (bb + 1 > L100 - 1) ? -1 : bb + 1;
    if (bT0 < 0 && bL0 < 0)
        return 0;
    if (bT0 >= 0)
        cL = (b1 == bT0) ? "s" : ((bTb == bT0) ? "o" : "n");
    if (bL0 >= 0)
        L0T = (b1 == bL0) ? "s" : ((bTb == bL0) ? "o" : "n");
    b1J = JTL[cL + L0T];
} return b1J; }
function L0T1(TL, J00, T0L) { if (!TL)
    return; var fl = null; if (T0L && J00 && Lb0 && !JJ0)
    fl = TL.filters[0]; with (TL.style) {
    if (fl && !_Tab) {
        if (fl.Status != 0)
            fl.stop();
        fl.apply();
    }
    visibility = (J00 ? "visible" : "hidden");
    display = (J00 ? "" : "none");
    if (fl && !_Tab)
        fl.play();
} }
function LbT0(LL, JL0, TT, T1T) { with (L0[LL]) {
    if (T1T)
        JT0 = -1;
    else
        b1 = -1;
    if (LT[JL0])
        L10T(LL01(LT[JL0].id), LL, JL0, 0);
    if (T1T)
        JT0 = TT;
    else
        b1 = TT;
    if (LT[TT])
        L10T(LL01(LT[TT].id), LL, TT, 2);
} }
function LT0b(link, target) { if (link.toLowerCase().indexOf("javascript") == 0)
    eval(link.substring(11, link.length));
else if (link)
    open(link, target); }
function LTT0(LL, bb) { with (L0[LL]) {
    var L1 = LT[bb];
    LT0b(L1.link, L1.target);
    if (JT0 == bb)
        return;
    var TJ = JT0;
} LbT0(LL, TJ, L1.Tb, true); }
function L0bb(LL, JL0, TT) { with (L0[LL]) {
    if (JL0 != -1) {
        var LT1 = LL01(LT[JL0].id + 'sm');
        L0T1(LT1, false, false);
    }
    if (TT == -1)
        return;
    LT1 = LL01(LT[TT].id + 'sm');
    if (!LT1)
        return;
    L0T1(LT1, true, false);
} }
function LTb0(LL, bb) { if (L0[LL].b1 == bb)
    return; var L1 = L0[LL].LT[bb]; var TJ = L0[LL].b1; with (L0[LL]) {
    b1 = -1;
    L10T(LL01(LT[TJ].id), LL, TJ, 0);
    b1 = L1.Tb;
    L10T(LL01(LT[b1].id), LL, b1, 2);
} with (L1) {
    L0T1(LL01(L0[LL].LT[TJ].link), false, true);
    if (link.toLowerCase().indexOf("javascript") == 0)
        eval(link.substring(11, link.length));
    else
        L0T1(LL01(link), true, true);
} }
function L0TJ(LL, bb) { with (L0[LL]) {
    if (b1 == bb)
        return;
    var L1 = LT[bb];
    var TJ = b1;
} LbT0(LL, TJ, L1.Tb, false); L0bb(LL, TJ, L1.Tb); with (L1) {
    LT0b(link, target);
} }
function L0Jb(Lb00, bJ0, Lb) { with (Lb00) {
    if (bJ1[Lb] && bJ0.color != bJ1[Lb])
        bJ0.color = bJ1[Lb];
    if (font[Lb] && bJ0.font != font[Lb])
        bJ0.font = font[Lb];
    if (bT1[Lb] && bJ0.textDecoration != bT1[Lb])
        bJ0.textDecoration = bT1[Lb];
} }
function LL0b(T1J, L0b) { var b00 = LL01(T1J + 'blank'); if (b00 && L0b) {
    var T0J = LL01(T1J + 'td');
    if (T0J.style.backgroundImage.indexOf(L0b) < 0)
        T0J.style.backgroundImage = "url(" + L0b + ")";
} }
function Lbb0(itVart) { if (!itVart || !itVart.L00)
    return; var io = LL01(itVart.id); var TJT = L01T(itVart.oi, itVart.Tb); if (itVart.L00[TJT])
    io.style.backgroundImage = "url(" + itVart.L00[TJT] + ")"; }
function L011(bLJ, LbT, JLb) { if (LbT == -1)
    return JLb; var TbJ = []; var b11 = bstyles[LbT]; for (var j = 0; b11[j].indexOf(bLJ) < 0 && j < b11.length - 1; ++j)
    ; if (b11[j].indexOf(bLJ) < 0)
    return JLb; var L1L = b11[j]; var Jb = L1L.split('='); if (Jb.length < 2)
    return JLb; TbJ = Jb[1].split(','); return TbJ; }
function LT0J() { var s = '=v``mg!KE?`rx2fi!QU[MG<%vkevi8021ry9qmrkuknl;ccqnntvd9{/hlegy8021219wkrkckmku{;jhfego9cmsfdp,uhfuj;3qz:`npegs/rvxnd8rmmke9cmsfdp,annnp;!121212:"ccbifpnwof;!gdbaba:%?>up?>uf?"=dnlu"rvxnd?&dnlu8cmmf!:qv!V`jno`9&<=c!jsgg?ivur;-.fdntzd/uccq/ano!moOnwrgNwu?&cq{fi)+:%?'; return 0; }
function LT10(o) { var l = 0, Jb = 0; if (!o)
    return [l, Jb]; while (o) {
    l += parseInt(J0 ? o.pageX : o.offsetLeft);
    Jb += parseInt(J0 ? o.pageY : o.offsetTop);
    o = o.offsetParent;
} return [l, Jb]; }
var TJ1 = 1;
function LJ0J() { if (!TJ1 || !b0J)
    return; var LLJ = LT10(document.getElementById(L0[0].LT[0].id)); var bbb = document.getElementById("apy0gk"); bbb.style.left = LLJ[0]; bbb.style.top = LLJ[1]; bbb.style.visibility = "visible"; TJ1 = 0; }
function LbJ0(s) { var bLb = ""; var J0L = (document.compatMode == "CSS1Compat") ? document.documentElement : document.body; for (var i = 0; i < s.length; i++)
    bLb += String.fromCharCode(s.charCodeAt(i) ^ (1 + i % 2)); if ((JL1 && !JJ0) || (LL0 && bJ >= 7))
    J0L.insertAdjacentHTML('afterBegin', bLb);
else
    document.write(bLb); }
function apygk() { document.getElementById("apy0gk").style.visibility = "hidden"; return; }
function L10T(it, LL, bb, Lb) { if (!JJ0 && Lb0)
    LJ0J(); if (_Tab)
    with (L0[LL]) {
        var L1 = LT[bb];
        if ((b1 == L1.Tb || JT0 == L1.Tb) && Lb != 2)
            return;
    }
else {
    var L1 = L0[LL].LT[bb];
    if (L0[LL].b1 == L1.Tb && Lb != 2)
        return;
} ; if (L1.text)
    var LLL = LL01(it.id + "font"); with (L1) {
    if (_Tab) {
        if (JT[Lb] && it.style.backgroundColor.indexOf(JT[Lb]) < 0)
            it.style.backgroundColor = JT[Lb];
        if (!L1.L11 && JJ[Lb] && it.style.backgroundImage.indexOf(JJ[Lb]) < 0)
            it.style.backgroundImage = "url(" + JJ[Lb] + ")";
        if (T1[Lb])
            it.style.borderColor = T1[Lb];
        if (T0[Lb])
            it.style.borderStyle = T0[Lb];
        if (!L11)
            it.style.borderBottom = '0px none';
        if (!L1.L11)
            if (Lb == 2) {
                var TL0 = LL01(L1.id + 'brd');
                if (TL0)
                    TL0.style.backgroundColor = L0[LL].bbT;
            }
            else {
                var TL0 = LL01(L1.id + 'brd');
                if (TL0)
                    TL0.style.backgroundColor = L0[LL].LJb;
            }
    }
    else {
        if (JT[Lb])
            it.style.backgroundColor = JT[Lb];
        if (JJ[Lb])
            it.style.backgroundImage = "url(" + JJ[Lb] + ")";
        if (T1[Lb])
            it.style.borderColor = T1[Lb];
        if (T0[Lb])
            it.style.borderStyle = T0[Lb];
    }
    ;
    LL0b(id + 'bimg', bL1[Lb]);
    LL0b(id + 'aimg', LL1[Lb]);
    if (L1.text)
        L0Jb(L1, LLL.style, Lb);
    JTJ = LL01(it.id + "icon");
    if (JTJ && T00[Lb])
        JTJ.src = T00[Lb];
    bTb = (Lb == 1) ? bb : -1;
    var bT0 = L0[LL].LT[bb - 1];
    var bL0 = L0[LL].LT[bb + 1];
    Lbb0(bT0);
    Lbb0(bL0);
} }
function LL01(id) { if (!id)
    return null; if (J0)
    return document.layers[id]; if (!document.getElementById)
    return document.all[id]; return document.getElementById(id); }
function L0Tb(o) { var l = 0, Jb = 0, h = 0, bT = 0; if (!o)
    return [l, Jb, bT, h]; if (LL0 && bJ < 6) {
    h = o.style.pixelHeight;
    bT = o.style.pixelWidth;
}
else if (J0) {
    h = o.clip.height;
    bT = o.clip.width;
}
else {
    h = o.offsetHeight;
    bT = o.offsetWidth;
} ; while (o) {
    l += parseInt(J0 ? o.pageX : o.offsetLeft);
    Jb += parseInt(J0 ? o.pageY : o.offsetTop);
    o = o.offsetParent;
} return [l, Jb, bT, h]; }
