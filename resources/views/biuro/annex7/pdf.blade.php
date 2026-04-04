<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 7.5pt;
        color: #000;
    }

    .header-bar {
        font-size: 6.5pt;
        color: #555;
        margin-bottom: 2px;
    }

    hr { border: none; border-top: 1px solid #555; margin: 2px 0; }

    .annex-title {
        text-align: center;
        font-size: 8pt;
        font-weight: bold;
        margin-bottom: 1px;
    }

    .annex-no {
        text-align: center;
        font-size: 10pt;
        font-weight: bold;
        margin-bottom: 1px;
    }

    .annex-subtitle {
        text-align: center;
        font-size: 7pt;
        margin-bottom: 2px;
    }

    .consignment {
        font-size: 6.5pt;
        margin-bottom: 3px;
    }

    table.main {
        width: 100%;
        border-collapse: collapse;
        font-size: 7.5pt;
    }

    table.main td {
        border: 1px solid #000;
        padding: 3px 4px;
        vertical-align: top;
        line-height: 12pt;
    }

    .field-label {
        font-weight: bold;
        font-size: 7pt;
    }

    .sig-row td {
        height: 14pt;
    }

    .center { text-align: center; }

    .footnotes {
        font-size: 6pt;
        line-height: 9pt;
        margin-top: 3px;
        color: #000;
    }
</style>
</head>
<body>

<div class="header-bar">
    I.190/88 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    Official Journal of the European Union
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    12.7.2006
</div>
<hr>

<div class="annex-title">ANNEX VII</div>
<div class="annex-no">EK: {{ $annex7->id }}</div>
<div class="annex-subtitle">INFORMATION ACCOMPANYING SHIPMENTS OF WASTE AS REFERRED TO IN ARTICLE 3(2) AND (4)</div>
<div class="consignment">Consignment information <sup>(1)</sup></div>

<table class="main">

    {{-- RZĄD 1: 1 i 2 --}}
    <tr>
        <td style="width:44%;">
            <strong>1. Person who arranges the shipment:</strong><br>
            Name: {{ $annex7->arranger->name }}<br>
            Address: {{ $annex7->arranger->address }}<br>
            Contact person: {{ $annex7->arranger->contact }}<br>
            Tel.: {{ $annex7->arranger->tel }}<br>
            E-mail: {{ $annex7->arranger->mail }}
        </td>
        <td colspan="2" style="width:56%;">
            <strong>2. Importer/consignee</strong><br>
            Name: {{ $annex7->importer->name }}<br>
            Address: {{ $annex7->importer->address }}<br>
            Contact person: {{ $annex7->importer->contact }}<br>
            Tel.: {{ $annex7->importer->tel }}<br>
            E-mail: {{ $annex7->importer->mail }}
        </td>
    </tr>

    {{-- RZĄD 2: 3 i 4 --}}
    <tr>
        <td style="width:50%;">
            <strong>3. Actual quantity:</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; t: .................
        </td>
        <td colspan="2" style="width:56%;">
            <strong>4. Actual date of shipment:</strong> {{ $annex7->date_shipment->format('d.m.Y') }}
        </td>
    </tr>

    {{-- RZĄD 3: 5a, 5b, 5c --}}
    <tr>
        <td style="width:40%;">
            <strong>5 (a) <sup>(2)</sup> First carrier:</strong><br>
            Name: {{ $annex7->carrier->name }}<br>
            Address: {{ $annex7->carrier->address }}<br>
            Contact person: {{ $annex7->carrier->contact }}<br>
            Tel.: {{ $annex7->carrier->tel }}<br>
            E-mail: {{ $annex7->carrier->mail }}<br>
            Means of transport: {{ $annex7->carrier_means_of_transport }}<br>
            Date of transfer: {{ $annex7->carrier_date_transfer?->format('d.m.Y') }}<br>
            Signature:
        </td>
        <td style="width:30%;">
            <strong>5 (b) Second carrier:</strong>
        </td>
        <td style="width:30%;">
            <strong>5 (c) Third carrier:</strong>
        </td>
    </tr>

    {{-- RZĄD 4: 6 (rowspan=2) + 8 --}}
    <tr>
        <td rowspan="2" style="width:50%;">
            <strong>6. Waste generator <sup>(3)</sup><br>
            Original producer(s), new producer(s) or collector:</strong><br>
            Name: {{ $annex7->generator->name }}<br>
            Address: {{ $annex7->generator->address }}<br>
            Contact person: {{ $annex7->generator->contact }}<br>
            Tel.: {{ $annex7->generator->tel }}<br>
            E-mail: {{ $annex7->generator->mail }}
        </td>
        <td colspan="2" style="width:50%;">
            <strong>8. Recovery operation (or if appropriate disposal operation<br>
            in the case of waste referred to in Article 3 (4)):</strong><br>
            R-code/D code: {{ $annex7->recoveryOperation->code }}
        </td>
    </tr>

    {{-- RZĄD 5: (kontynuacja rowspan 6) + 9 --}}
    <tr>
        <td colspan="2" style="width:50%;">
            <strong>9. Usual description of the waste:</strong><br>
            {{ $annex7->wasteDescription->description }}
        </td>
    </tr>

    {{-- RZĄD 6: 7 i 10 --}}
    <tr>
        <td style="width:50%;">
            <strong>7. Recovery facility:</strong><br>
            Name: {{ $annex7->recovery->name }}<br>
            Address: {{ $annex7->recovery->address }}<br>
            Contact person: {{ $annex7->recovery->contact }}<br>
            Tel.: {{ $annex7->recovery->tel }}<br>
            E-mail: {{ $annex7->recovery->mail }}
        </td>
        <td colspan="2" style="width:50%;">
            <strong>10. Waste identification (fill in relevant codes):</strong><br>
            i) &nbsp;&nbsp; Basel Annex IX:<br>
            ii) &nbsp; OECD (if different from (i)):<br>
            iii) Annex III A <sup>(4)</sup>:<br>
            iv) &nbsp;Annex III B <sup>(5)</sup>:<br>
            v) &nbsp;&nbsp;EC list of wastes:<br>
            vi) &nbsp;National code: {{ $annex7->wasteCode->code }}<br>
            vii) other (please specify):
        </td>
    </tr>

    {{-- RZĄD 7: 11 --}}
    <tr>
        <td colspan="3">
            <strong>11. Countries/states concerned:</strong>
        </td>
    </tr>

    <tr class="sig-row">
        <td colspan="3" class="center">
            Export/dispatch &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Transit &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Import/destination
        </td>
    </tr>

    <tr class="sig-row">
        <td colspan="3" class="center">
            Poland
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Germany
        </td>
    </tr>

    {{-- RZĄD 8: 12 --}}
    <tr>
        <td colspan="3">
            <strong>12. Declaration of the person who arranges the shipment:</strong>
            I certify that the above information is complete and correct to my best knowledge. I also certify that effective written contractual obligations have been entered into with the consignee (not required in the case of waste referred to in Article 3 (4)).<br><br>
            Name: {{ $annex7->arranger->contact }}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Date: {{ $annex7->date_shipment->format('d.m.Y') }}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Signature:
        </td>
    </tr>

    {{-- RZĄD 9: 13 --}}
    <tr>
        <td colspan="3">
            <strong>13. Signature upon receipt of the waste by the consignee:</strong><br><br><br>
            Name:
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Date:
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Signature:
        </td>
    </tr>

    {{-- RZĄD 10: nagłówek --}}
    <tr>
        <td colspan="3" class="center">
            <strong>TO BE COMPLETED BY THE RECOVERY FACILITY OR BY THE LABORATORY:</strong>
        </td>
    </tr>

    {{-- RZĄD 11: 14 --}}
    <tr>
        <td colspan="3">
            <strong>14. Shipment received at recovery facility &#x25A1; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; or laboratory &#x25A1;</strong>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Quantity received: &nbsp; t: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; m3:<br><br>
            Name:
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Date:
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Signature:
        </td>
    </tr>

</table>

<div class="footnotes">
    <sup>(1)</sup> Information accompanying shipments of green listed waste and destined for recovery or waste destined for laboratory analysis pursuant to Regulation (EC) No.1013/2006.<br>
    <sup>(2)</sup> If more than three carriers, attach information as required in blocks 5 (a), (b) and (c).<br>
    <sup>(3)</sup> When the person who arranges the shipment is not the producer or collector, information about the producer or collector shall be provided.<br>
    <sup>(4)</sup> The relevant code(s) as indicated in Annex IIIA to Regulation (EC) No 1013/2006 are not to be used, as appropriate in sequence. Certain Basel entries as B1100, B3010 and B3020 are restricted to particular waste streams only, as indicated in Annex IIIA.<br>
    <sup>(5)</sup> The BEU Codes listed in Annex IIIB to Regulation (EC) No 1013/2006 are to be used.
</div>

</body>
</html>
