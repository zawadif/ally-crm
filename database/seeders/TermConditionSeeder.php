<?php

namespace Database\Seeders;

use App\Enums\PrivacyTermEnum;
use Illuminate\Database\Seeder;
use App\Models\PrivacyPoliciesAndTermCondition;

class TermConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $termConditionData = array();
        $termConditionData['title'] = "Term & Condition";
        $termConditionData['type'] = PrivacyTermEnum::TERMS_AND_CONDITIONS;
        $termConditionData['description'] = "General
By using our website, you agree to the Terms of Use. We may change or update these terms so please check this page regularly. We do not represent or warrant that the information on our web site is accurate, complete, or current. This includes pricing and availability information. We reserve the right to correct any errors or omissions, and to change or update information at any time without giving prior notice.

Correction of Errors and Inaccuracies
The information on the site may contain typographical errors or inaccuracies and may not be complete or current. The Main Label therefore reserves the right to correct any errors, inaccuracies or omissions and to change or update information at any time with or without prior notice (including after you have submitted your order). Please note that such errors, inaccuracies or omissions may relate to product description, pricing, product availability, or otherwise.

Tax
As a seller, you are responsible for collecting and paying taxes associated with any profits made through The Main Label. The Main Label will issue 1099-K forms to all sellers in the United States who receive more than $600.00 or $2,000.00 in profits to comply with IRS requirements. All forms will be mailed to the address you’ve indicated in your profile by January 31 for the previous year.

Return Policy
Since we only manufacture what is ordered, The Main Label does not accept returns or exchanges at this time. All instant purchases should be discussed directly with the seller you purchased from as they are responsible for their own shop policies. If you are unhappy with your order for any reason at all, please contact The Main Label at customerservice@themainlabel.com and we’ll work with you to make it right. We will not accept any packages sent without authorization, any shipments received that have not been authorized will be refused/shipped back. Please make sure that you have carefully reviewed your order prior to finalizing your purchase.

Cancellations
If you decide that you no longer want your order for any reason you may cancel it as long as the campaign period is still active. However, once a campaign ends, we are unable to cancel an order as the information has already been sent to the printer for manufacturing and fulfillment.

Colors
We have made the strongest of efforts to display all product colors that appear on the Site as accurately as possible. However, as the actual colors you see will depend on your monitor and/or other technological circumstance, we cannot and do not guarantee that your monitor's display of any color will be accurate.

Product Availability
Although availability may be indicated on our site, we cannot guarantee product availability or immediate delivery. We reserve the right, without liability or prior notice to revise, discontinue, or cease to make available any or all products or to cancel any order.

Shipping & Delivery
For all orders within North America, please allow approximately 14 business days from the time a campaign ends (please note this is different from the time of purchase) to receive your order. For all international orders, please allow approximately 21 business days from the time a campaign ends (please note this is different from the time of purchase) to receive your order. You will receive an email from The Main Label when your order has been confirmed. If you still have not received your purchase after the above mentioned times, please notify tennisfightadmin@tennisfight.com.
";
        PrivacyPoliciesAndTermCondition::create($termConditionData);
    }
}
