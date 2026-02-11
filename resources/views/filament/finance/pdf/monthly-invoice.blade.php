<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Invoice - {{ $student->name }}</title>
    <style>
        /* @font-face {
        font-family: SourceSansPro;
        src: url(SourceSansPro-Regular.ttf);
        } */

        .clearfix:after {
        content: "";
        display: table;
        clear: both;
        }

        a {
        text-decoration: none;
        }

        body {
        position: relative;
        /* width: 21cm;  
        height: 29.7cm;  */
        margin: 0 auto; 
        color: #000;
        background: #FFFFFF; 
        font-family: 'Calibri', sans-serif; 
        font-size: 14px; 
        font-weight: bold; 
        /* font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; */
        }

        header {
        /* padding: 10px 0; */
        margin-bottom: 10px;
        border-bottom: 1px solid #AAAAAA;
        }

        #logo {
        float: left;
        /* margin-top: 8px; */
        }

        #logo img {
        height: 60px;
        }

        #company {
        float: right;
        text-align: left;
        }


        #details {
        margin-bottom: 10px;
        }

        #client {
        padding-left: 6px;
        border-left: 6px solid #0087C3;
        float: left;
        }

        #client .to {
        color: #000;
        }

        h2.name {
        font-size: 1.4em;
        /* font-weight: normal; */
        margin: 0;
        }

        #invoice {
        float: right;
        text-align: right;
        }

        #invoice h1 {
        color: #000;
        font-size: 14px;
        line-height: 1em;
        /* font-weight: normal; */
        margin: 0  0 10px 0;
        }

        #invoice .date {
        font-size: 1.1em;
        color: #000;
        font-weight: bold;
        }

        table#det {
        border: 1px solid black;
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 10px;
        
        }

        table#det th,
        table#det td {
        padding: 5px;
        border: 1px solid black;
        text-align: center;
        /* border-bottom: 1px solid #FFFFFF; */
        }

        table#det th {
        white-space: nowrap;        
        /* font-weight: normal; */
        }

        table#det td {
        text-align: right;
        }

        table#det td h3{
        color: #000;
        font-size: 12px;
        font-weight: bold;
        margin: 0 0 0.2em 0;
        }

        

        table#det .desc {
        text-align: left;
        }


        table#det tfoot td {
        /* padding: 10px 20px; */
        background: #FFFFFF;
        border-bottom: none;
        font-size: 12px;
        white-space: nowrap; 
        /* border-top: 1px solid #AAAAAA;  */
        }

        table#det tfoot tr:first-child td {
        border-top: none; 
        }


        table#det tfoot tr td:first-child {
        border: none;
        }

        #thanks{
        font-size: 2em;
        margin-bottom: 50px;
        }

        #notices{
        padding-left: 6px;
        border-left: 6px solid #0087C3;  
        }

        #notices .notice {
        font-size: 12px;
        }
        p{
            margin:0
        }
        .no {
            text-align: center;
        }
    </style>
  </head>
  <body>
    <header class="clearfix">
      <div id="logo">
        <img src="logo.png">
        <h2 class="name">SEKOLAH BASIC</h2>
        <p>Jl. Laksamana Kawasan Industri No.1 | (0778) 460817</p>
      </div>
      <div id="company">
        <h2 class="name">Bukti Pembayaran</h2>
        <table style="border:none">
            <tr>
                <td style="text-align:left">Name</td>
                <td style="text-align:left">:</td>
                <td style="text-align:left">{{$student->name}}</td>
            </tr>
            <tr>
                <td style="text-align:left">Print At</td>
                <td style="text-align:left">:</td>
                <td style="text-align:left">{{ now()->format('d-m-Y H:i') }}</td>
            </tr>
        </table>
      </div>
    </header>
    <main>
      <table id="det" border="0" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th class="no">#</th>
            <th class="desc">Description</th>
            <th class="qty">Price</th>
            <th class="unit">Fine</th>
            <th class="qty">Discount</th>
            <th class="total">TOTAL</th>
          </tr>
        </thead>
        <tbody>
            @php 
                $total = 0;
            @endphp
            @foreach($invoices as $invoice)
            <tr>
                <td class="no">{{$loop->iteration}}</td>
                <td class="desc">
                  <h3>
                    SPP bln {{$invoice->month?->name}} 
                    TA {{$invoice->school_year_name}} ({{$invoice->classroom_name}})<br>
                    Via {{$invoice->payment_method?->name ?? '-'}}<br>
                    Tgl Bayar {{$invoice->paid_at}}
                  </h3>
                </td>
                <td class="qty">{{$invoice->formatted_amount}}</td>
                <td class="unit">{{$invoice->formatted_fine}}</td>
                <td class="qty">{{$invoice->formatted_discount}}</td>
                <td class="total">
                    @php 
                        $total += $invoice->total_amount;
                    @endphp

                    {{$invoice->formatted_total_amount}}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
          <!-- <tr>
            <td colspan="2"></td>
            <td colspan="2">SUBTOTAL</td>
            <td>$5,200.00</td>
          </tr>
          <tr>
            <td colspan="2"></td>
            <td colspan="2">TAX 25%</td>
            <td>$1,300.00</td>
          </tr> -->
          <tr>
            <!-- <td colspan="2"></td> -->
            <td colspan="5">GRAND TOTAL</td>
            <td>{{'Rp '.Number::format($total, locale: 'id')}}</td>
          </tr>
        </tfoot>
      </table>
      <!-- <div id="thanks">Thank you!</div>
      <div id="notices">
        <div>NOTICE:</div>
        <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
      </div> -->
      <div id="logo">
        <p><i>Terbilang:<br> {{Number::spell($total,'id')}} rupiah</i></p>
        <br><br>
        <p>Cashier</p>
        <br>
        <br>
        <br>
        <p>{{ Str::before(auth()->user()?->name ?? 'System', ' ') }}</p>
      </div>
      <div id="company">
        <ul style="font-size:12px;list-style-type: none;">
            <li>1. Uang yang telah dibayarkan tidak dapat ditarik kembali!</li>
            <li>2. Pembayaran SPP yang telat akan dikenakan denda Rp {{config('app.fine')}}/hari</li>
        </ul>
      </div>
    </main>
  </body>
</html>