<? $this->md5_compil='4bf0e616ca02aab1539eb16b55513680'; ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>

<title>Квитанция <?php if(isset($this->vars['order']['order_id_str'])) echo $this->vars['order']['order_id_str']; ?></title>
<meta http-equiv=content-type content="text/html;  charset=utf-8">
  <link href="/plugins/invoice_fl/style.css" rel="stylesheet" />
</head>
<body onload="window.print(); window.close()">

<div align=center>
    <br>
    <table cellspacing=0 cellpadding=4 width=680 border=1>
        <tbody>
            <tr>
                <td valign=bottom width="30%">
                    <p align=right>Извещение</p>
                    <p align=right>&nbsp;</p>
                    <p align=right>&nbsp;</p>
                    <p align=right>&nbsp;</p>
                    <p align=right>&nbsp;</p>
                    <p align=right>&nbsp;</p>
                    <p align=right>&nbsp;</p>
                    <p align=right>Кассир</p>
                </td>
                <td width="70%">

                    <table cellspacing=0 cellpadding=2 width="100%" border=0>
                        <tbody>
                            <tr>
                                <td colspan=3><strong>Получатель платежа</strong></td>
                            </tr>
                            <tr>
                                <td colspan=3>Наименование:&nbsp;<?php if(isset($this->vars['settings']['company_name'])) echo $this->vars['settings']['company_name']; ?></td>
                            </tr>
                            <tr>
                                <td>счет:&nbsp;<?php if(isset($this->vars['settings']['bank_account_number'])) echo $this->vars['settings']['bank_account_number']; ?></td>
                                <td>инн:&nbsp;<?php if(isset($this->vars['settings']['inn'])) echo $this->vars['settings']['inn']; ?></td>
                                <td>кпп:&nbsp;<?php if(isset($this->vars['settings']['kpp'])) echo $this->vars['settings']['kpp']; ?></td>
                            </tr>
                            <tr>
                                <td colspan=3>Наименование банка:&nbsp;<?php if(isset($this->vars['settings']['bank_name'])) echo $this->vars['settings']['bank_name']; ?></td>
                            </tr>
                            <tr>
                                <td>кор.&nbsp;счет:&nbsp;<?php if(isset($this->vars['settings']['bank_kor_number'])) echo $this->vars['settings']['bank_kor_number']; ?></td>
                                <td colspan=2>бик:&nbsp;<?php if(isset($this->vars['settings']['bik'])) echo $this->vars['settings']['bik']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                   
                   
                    <table cellspacing=0 cellpadding=2 width="100%" border=0>
                        <tbody>
                            <tr>
                                <td><strong>Плательщик</strong></td>
                            </tr>
                            <tr>
                                <td class="inline_edit"><?php if(isset($this->vars['contact']['surname'])) echo $this->vars['contact']['surname']; ?> <?php if(isset($this->vars['contact']['name'])) echo $this->vars['contact']['name']; ?> <?php if(isset($this->vars['contact']['lastname'])) echo $this->vars['contact']['lastname']; ?></td>
                            </tr>
                            <tr>
                                <td class="inline_edit"><?php if(isset($this->vars['order']['short_address'])) echo $this->vars['order']['short_address']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <table cellspacing=0 cellpadding=2 width="100%" border=1>
                        <tbody>
                        <tr>
                            <td><div align=center>Назначение платежа</div></td>
                            <td><div align=center>Дата</div></td>
                            <td><div align=center>Сумма</div></td>
                        </tr>
                        <tr>
                            <td><div align=center class="inline_edit">Оплата заказа в магазине Русконнект #<?php if(isset($this->vars['order']['order_id'])) echo $this->vars['order']['order_id']; ?></div></td>
                            <td><div align=center>&nbsp;<?php if(isset($this->vars['date'])) echo $this->vars['date']; ?></div></td>
                            <td><div align=center class="inline_edit"><?php if(isset($this->vars['cartbox']['sum'])) echo $this->vars['cartbox']['sum']; ?></div></td>
                        </tr>
                        </tbody>
                    </table>
                    <p>Подпись плательщика:</p>

                </td>
            </tr>
            <tr>
                <td valign=bottom>
                    <p align=right>Квитанция</p>
                    <p align=right>&nbsp;</p>
                    <p align=right>&nbsp;</p>
                    <p align=right>&nbsp;</p>
                    <p align=right>&nbsp;</p>
                    <p align=right>&nbsp;</p>
                    <p align=right>&nbsp;</p>
                    <p align=right>Кассир</p>
                </td>
                <td>
                    <table cellspacing=0 cellpadding=2 width="100%" border=0>
                        <tbody>
                            <tr>
                                <td colspan=3><strong>Получатель платежа</strong></td>
                            </tr>
                            <tr>
                                <td colspan=3>Наименование:&nbsp;<?php if(isset($this->vars['settings']['company_name'])) echo $this->vars['settings']['company_name']; ?></td>
                            </tr>
                            <tr>
                                <td>счет:&nbsp;<?php if(isset($this->vars['settings']['bank_account_number'])) echo $this->vars['settings']['bank_account_number']; ?></td>
                                <td>инн:&nbsp;<?php if(isset($this->vars['settings']['inn'])) echo $this->vars['settings']['inn']; ?></td>
                                <td>кпп:&nbsp;<?php if(isset($this->vars['settings']['kpp'])) echo $this->vars['settings']['kpp']; ?></td>
                            </tr>
                            <tr>
                                <td colspan=3>Наименование банка:&nbsp;<?php if(isset($this->vars['settings']['bank_name'])) echo $this->vars['settings']['bank_name']; ?></td>
                            </tr>
                            <tr>
                                <td>кор.&nbsp;счет:&nbsp;<?php if(isset($this->vars['settings']['bank_kor_number'])) echo $this->vars['settings']['bank_kor_number']; ?></td>
                                <td colspan=2>бик:&nbsp;<?php if(isset($this->vars['settings']['bik'])) echo $this->vars['settings']['bik']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                   
                   
                    <table cellspacing=0 cellpadding=2 width="100%" border=0>
                        <tbody>
                            <tr>
                                <td><strong>Плательщик</strong></td>
                            </tr>
                            <tr>
                                <td class="inline_edit"><?php if(isset($this->vars['contact']['surname'])) echo $this->vars['contact']['surname']; ?> <?php if(isset($this->vars['contact']['name'])) echo $this->vars['contact']['name']; ?> <?php if(isset($this->vars['contact']['lastname'])) echo $this->vars['contact']['lastname']; ?></td>
                            </tr>
                            <tr>
                                <td class="inline_edit"><?php if(isset($this->vars['order']['short_address'])) echo $this->vars['order']['short_address']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <table cellspacing=0 cellpadding=2 width="100%" border=1>
                        <tbody>
                        <tr>
                            <td><div align=center>Назначение платежа</div></td>
                            <td><div align=center>Дата</div></td>
                            <td><div align=center>Сумма</div></td>
                        </tr>
                        <tr>
                            <td><div align=center class="inline_edit">Оплата заказа в магазине Русконнект #<?php if(isset($this->vars['order']['order_id'])) echo $this->vars['order']['order_id']; ?></div></td>
                            <td><div align=center>&nbsp;<?php if(isset($this->vars['date'])) echo $this->vars['date']; ?></div></td>
                            <td><div align=center class="inline_edit"><?php if(isset($this->vars['cartbox']['sum'])) echo $this->vars['cartbox']['sum']; ?></div></td>
                        </tr>
                        </tbody>
                    </table>
                    <p>Подпись плательщика:</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>
