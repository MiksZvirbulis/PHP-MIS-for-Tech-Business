--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `start_time` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `finish_time` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `start_timestamp` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `finish_timestamp` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `start_ip_address` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `finish_ip_address` varchar(15) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;


--
-- Table structure for table `credit_notes`
--

CREATE TABLE `credit_notes` (
  `id` int(6) UNSIGNED ZEROFILL NOT NULL,
  `purchase_id` int(5) UNSIGNED ZEROFILL NOT NULL,
  `credit_note_number` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `rma_id` int(7) UNSIGNED ZEROFILL NOT NULL,
  `date` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `vat` tinyint(1) NOT NULL,
  `shipping_charges` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `other_charges` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `note` text COLLATE latin1_general_ci NOT NULL,
  `flag` tinyint(1) NOT NULL,
  `highlight` tinyint(1) NOT NULL,
  `query` tinyint(1) NOT NULL,
  `credit_note_matched` tinyint(1) NOT NULL,
  `date_created` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `created_by_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `credit_note_items`
--

CREATE TABLE `credit_note_items` (
  `id` int(11) NOT NULL,
  `upc` int(6) UNSIGNED ZEROFILL NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_exc_vat` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `credit_note_id` int(6) UNSIGNED ZEROFILL NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_methods`
--

CREATE TABLE `delivery_methods` (
  `delivery_key` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `delivery_name` text COLLATE latin1_general_ci NOT NULL,
  `purchases` int(1) NOT NULL,
  `orders` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `delivery_methods`
--

INSERT INTO `delivery_methods` (`delivery_key`, `delivery_name`, `purchases`, `orders`) VALUES
('personal_delivery', 'Personal Delivery', 1, 1),
('collection', 'Collection', 1, 1),
('citylink', 'Citylink', 1, 0),
('dhlcourier', 'DHLCourier', 1, 0),
('dpd_courier', 'DPD Courier', 1, 1),
('fed_ex_courier', 'Fed Ex Courier', 1, 0),
('interlink', 'Interlink Express', 1, 1),
('pallet_delivery', 'Pallet Delivery', 1, 0),
('other', 'Other', 1, 0),
('parcelforce', 'Parcelforce', 1, 0),
('royal_mail', 'Royal Mail', 1, 1),
('ups_courier', 'UPS Courier', 1, 0),
('amtrak', 'Amtrak', 1, 0),
('international_courier', 'International Courier', 1, 1),
('hermes', 'Hermes', 1, 0),
('yodel', 'Yodel', 1, 0),
('amazon_logistics', 'Amazon Logistics', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `email_categories`
--

CREATE TABLE `email_categories` (
  `id` int(11) NOT NULL,
  `description` varchar(255) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `email_categories`
--

INSERT INTO `email_categories` (`id`, `description`) VALUES
(1, 'Shipment'),
(2, 'Warranty'),
(3, 'Sales'),
(4, 'Repairs'),
(5, 'Payments'),
(6, 'Orders');

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int(11) NOT NULL,
  `subject` text COLLATE latin1_general_ci NOT NULL,
  `parent_type` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `parent_id` int(6) UNSIGNED ZEROFILL NOT NULL,
  `sent_to` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `time` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `sent_by_id` int(11) NOT NULL,
  `sent` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `type` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `description` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `subject` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `content` text COLLATE latin1_general_ci NOT NULL,
  `created_by_id` int(11) NOT NULL,
  `last_updated_by_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `cat_id`, `type`, `description`, `subject`, `content`, `created_by_id`, `last_updated_by_id`) VALUES
(1, 1, 'order', 'Interlink Shipment', 'Order Ref: INV_NO - Your order has been shipped', 'Dear FIRST_LAST,\r\n\r\nOrder Ref: INV_NO\r\n\r\nWe are pleased to inform you that your order has been dispatched on SHIPMENT_DAY, SHIPMENT_DATE, by the Interlink Express Courier Service and is scheduled to be delivered to you on EST_DEL_DATE, between DPD_SERVICE_TAG.\r\n\r\nYour order has been shipped to the following address:\r\n\r\nFIRST_LAST\r\nSHIPPING_ADDRESS\r\n\r\nYour Interlink Express Delivery Tracking Number is: CLAN_REF\r\n\r\nYou can track the most up to date information about the progress of the delivery of your order by clicking the link below or copying it to a web browser:\r\n\r\nhttp://www.interlinkexpress.com/tracking/trackingSearch.do?search.searchType=1&search.consignmentNumber=CLAN_REF\r\n\r\nWe strongly advise you to check the above link and your email regularly (especially around 8am on the scheduled day of delivery) so that you can see the progress of your delivery and plan your day accordingly.\r\n\r\nIf it shows that your parcels is out on the delivery van then there is a strong likelihood that the item will be delivered on the day stated. If the status  link says the items are in transit / or on route to the depot, then it is unlikely that the order will be delivered to you on the day. \r\n\r\nInterlink Express do not provide us with an exact time for delivery. If possible Interlink Express may email you to provide a delivery time slot but this is more of a guide and Interlink Express may call at any point during the times specified at the top of this email. \r\n\r\nDelivery will be made the address specified by you prior to despatch and any subsequent redirections are chargeable by Interlink Express. The delivery will require a signature from someone at the address specified. This is strictly enforced for security reasons.\r\n\r\nIf you are unavailable on the scheduled delivery date, Interlink Express should leave a calling card and take the parcel back to your local depot. The delivery should then be automatically re-attempted the next working day. \r\n\r\nIf delivery still can not be made, your parcel will be held until you contact \r\nInterlink Express to rearrange delivery.\r\n\r\nInterlink Express can be contacted via their website which is http://www.interlinkexpress.com/\r\n\r\nWe hope you will receive your items in good condition. Upon receipt please unbox the items carefully. Please do not use knifes or other sharp objects to cut open the boxes. \r\n\r\nPlease retain all the boxes and packaging (including all foam or polystyrene inserts) for your PC and related items in case you ever need to send it back to us for warranty purposes. We would strongly advise you to retain all the packaging as we would otherwise charge you Â£10 to supply a replacement box and packaging.\r\n\r\nDespite our best efforts sometimes goods get damaged during delivery. If you receive your item in damaged condition or it is not working when it arrives please either call us on 020 8778 0090 or email us at info@tech-house.co.uk \r\n\r\nPlease notify us of any damage within 24-48hrs as damaged shipment claims will not be entertained after this time. Any faults reported after 48 hours of delivery will be treated as standard RTB Warranty claims.\r\n\r\nFinally, we would like to take this opportunity to thank you for choosing Tech-House Computers. We appreciate your business. We are a small growing business working hard to offer quality PC bargains coupled with exceptional customer service. As such if you are happy with your PC please show your thanks by recommending us to your family and friends...simply call 020 8778 0090 or click online at www.tech-house.co.uk\r\n\r\nKind Regards\r\n\r\nCustomer Services\r\nTech-House Computers', 2, 1),
(2, 4, 'receipt', 'Receipt Status Update', 'Status Update for your Receipt #RECEIPT_NO', 'Dear FIRST_LAST,\r\n\r\nReceipt Ref: #RECEIPT_NO\r\n\r\nThis email notification is being sent to inform you that the status of your repairs receipt has changed.\r\n\r\nThe current status of your receipt is: <b>STATUS</b>\r\n\r\nThis message is intended to keep you informed about the progress of the repair and you do not need to take any further action at this point, unless the above status actually states that you should.\r\n\r\nHowever, should you feel you need to contact us with regards to your receipt, please email us at info@tech-house.co.uk or call us on 020 8778 0090 for further assistance.\r\n\r\nOur opening hours:\r\n\r\nMonday to Friday - 10:00 to 18:00\r\nSaturday - 10:00 to 14:00\r\nSunday - Closed\r\n\r\n---\r\nKind Regards,\r\nTech House Computers\r\nhttp://tech-house.co.uk\r\nPh.02087780090', 1, 1),
(3, 3, 'receipt', 'Review Email - Receipt', 'Tech House Computers - Thank you', 'Dear FIRST_LAST,\r\n\r\nMany thanks for visiting Tech House Computers and using our services. Could you do us a favour by writing a short review on the following websites:\r\n\r\n- www.reviewcentre.com/Online-Computer-Shops/Tech-House-www-tech-house-co-uk-reviews_1801255\r\n- http://sydenham.org.uk/forum\r\n\r\nWe have had many happy customers over the last few years, however not a lot of customers have written any reviews for us. Since we are a small business who really work hard on our customer service, this is really frustrating. So we hope you will write a review to help us out. Please register before you write the review as registered reviews tend to be valued more by web users than guest reviews.\r\n\r\n---\r\nKind Regards,\r\nTech House Computers\r\nhttp://tech-house.co.uk\r\nPh.02087780090', 1, 1),
(4, 3, 'order', 'Review Email - Order', 'Tech House Computers - Thank you', 'Dear FIRST_LAST,\r\n\r\nMany thanks for visiting Tech House Computers and using our services. Could you do us a favour by writing a short review on the following websites:\r\n\r\n- www.reviewcentre.com/Online-Computer-Shops/Tech-House-www-tech-house-co-uk-reviews_1801255\r\n- http://sydenham.org.uk/forum\r\n\r\nWe have had many happy customers over the last few years, however not a lot of customers have written any reviews for us. Since we are a small business who really work hard on our customer service, this is really frustrating. So we hope you will write a review to help us out. Please register before you write the review as registered reviews tend to be valued more by web users than guest reviews.\r\n\r\n---\r\nKind Regards,\r\nTech House Computers\r\nhttp://tech-house.co.uk\r\nPh.02087780090', 1, 1),
(5, 6, 'order', 'Order Status Update', 'Status Update for your Order #ORDER_NO', 'Dear FIRST_LAST,\r\n\r\nOrder Ref: #ORDER_NO\r\n\r\nThis email notification is being sent to inform you that the status of your order has changed.\r\n\r\nThe current status of your receipt is: <b>STATUS</b>\r\n\r\nThis message is intended to keep you informed about the progress of the order and you do not need to take any further action at this point, unless the above status actually states that you should.\r\n\r\nHowever, should you feel you need to contact us with regards to your order, please email us at info@tech-house.co.uk or call us on 020 8778 0090 for further assistance.\r\n\r\nOur opening hours:\r\n\r\nMonday to Friday - 10:00 to 18:00\r\nSaturday - 10:00 to 14:00\r\nSunday - Closed\r\n\r\n---\r\nKind Regards,\r\nTech House Computers\r\nhttp://tech-house.co.uk\r\nPh.02087780090', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_number` int(6) UNSIGNED ZEROFILL NOT NULL,
  `order_id` int(6) UNSIGNED ZEROFILL NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `invoices`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `action` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `custom_action` text COLLATE latin1_general_ci NOT NULL,
  `parent_type` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `parent_id` int(6) UNSIGNED ZEROFILL NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;


--
-- Table structure for table `opening_stock`
--

CREATE TABLE `opening_stock` (
  `id` int(11) NOT NULL,
  `upc` int(6) UNSIGNED ZEROFILL NOT NULL,
  `quantity` int(11) NOT NULL,
  `reason` text COLLATE latin1_general_ci NOT NULL,
  `added` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `added_by_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(6) UNSIGNED ZEROFILL NOT NULL,
  `created` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `created_by_id` int(11) NOT NULL,
  `vat` decimal(18,2) NOT NULL,
  `order_type` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `first_name` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `last_name` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `company_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `mobile_number` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `home_number` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `work_number` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `order_ref` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `billing_line1` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `billing_line2` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `billing_line3` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `billing_line4` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `billing_postcode` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `shipping_line1` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `shipping_line2` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `shipping_line3` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `shipping_line4` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `shipping_postcode` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `priority` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `invoice_date` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `shipment_date` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `committed` int(1) NOT NULL,
  `order_status` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `build_service` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `delivery_method` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `delivery_instructions` text COLLATE latin1_general_ci NOT NULL,
  `saturday` int(1) NOT NULL,
  `number_of_parcels` int(4) NOT NULL,
  `warranty` int(11) NOT NULL,
  `shipping_exc_vat` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `upgrades_exc_vat` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `other_exc_vat` varchar(15) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_spec_id` int(11) NOT NULL,
  `upc` int(6) UNSIGNED ZEROFILL NOT NULL,
  `service_id` int(11) NOT NULL,
  `quantity` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `order_id` int(6) UNSIGNED ZEROFILL NOT NULL,
  `cost_exc_vat` varchar(10) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `order_notes`
--

CREATE TABLE `order_notes` (
  `id` int(11) NOT NULL,
  `order_id` int(6) UNSIGNED ZEROFILL NOT NULL,
  `note` text COLLATE latin1_general_ci NOT NULL,
  `date` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `added_by_id` int(11) NOT NULL,
  `invoice` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `order_specs`
--

CREATE TABLE `order_specs` (
  `spec_id` int(11) NOT NULL,
  `order_id` int(6) UNSIGNED ZEROFILL NOT NULL,
  `type` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `quantity` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `cost_exc_vat` varchar(10) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;


--
-- Table structure for table `payment_entries`
--

CREATE TABLE `payment_entries` (
  `id` int(11) NOT NULL,
  `payment_date` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `type` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `amount` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `reference` text COLLATE latin1_general_ci NOT NULL,
  `transfer_account_name` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `instore_card_type` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `cheque_number` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `cheque_cleared` tinyint(1) NOT NULL,
  `cheque_clearance` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `cheque_bank_name` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `order_id` int(6) UNSIGNED ZEROFILL NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;


--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `purchase_id` int(5) UNSIGNED ZEROFILL NOT NULL,
  `batch_number` int(5) UNSIGNED ZEROFILL NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `invoice_number` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `invoice_date` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `delivery_date` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `vat` int(1) NOT NULL,
  `shipping_charges` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `other_charges` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `amount_paid` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `note` text COLLATE latin1_general_ci NOT NULL,
  `short_shipment` int(1) NOT NULL DEFAULT '0',
  `flag` int(1) NOT NULL DEFAULT '0',
  `highlight` int(1) NOT NULL DEFAULT '0',
  `query` int(1) NOT NULL DEFAULT '0',
  `invoice_matched` int(1) NOT NULL DEFAULT '0',
  `delivery_method` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `created_by_id` int(11) NOT NULL,
  `date_created` varchar(25) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` int(11) NOT NULL,
  `upc` int(6) UNSIGNED ZEROFILL NOT NULL,
  `price_exc_vat` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `purchase_id` int(5) UNSIGNED ZEROFILL NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` int(6) UNSIGNED ZEROFILL NOT NULL,
  `customer_name` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `customer_surname` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `customer_email` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `customer_number` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `alternative_number` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `computer_information` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `scratches_on_body` int(1) NOT NULL DEFAULT '0',
  `scratches_on_screen` int(1) NOT NULL DEFAULT '0',
  `screws_missing` int(1) NOT NULL DEFAULT '0',
  `rubber_pad_missing` int(1) NOT NULL DEFAULT '0',
  `keyboard_btns_missing` int(1) NOT NULL DEFAULT '0',
  `charger_missing` int(1) NOT NULL DEFAULT '0',
  `battery_missing` int(1) NOT NULL,
  `checked` int(1) NOT NULL,
  `other_charges` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `discount` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `discount_reason` text COLLATE latin1_general_ci NOT NULL,
  `note` text COLLATE latin1_general_ci NOT NULL,
  `customer_note` text COLLATE latin1_general_ci NOT NULL,
  `amount_paid` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `payment_type` varchar(4) COLLATE latin1_general_ci NOT NULL,
  `estimated_collection` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `added` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `date_collected` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `created_by_id` int(11) NOT NULL,
  `status` text COLLATE latin1_general_ci NOT NULL,
  `invoiced` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `receipt_items` (
  `id` int(11) NOT NULL,
  `receipt_id` int(6) UNSIGNED ZEROFILL NOT NULL,
  `service_id` int(11) NOT NULL,
  `price` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `computer_quantity` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `rma`
--

CREATE TABLE `rma` (
  `id` int(7) UNSIGNED ZEROFILL NOT NULL,
  `date_created` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `supplier_rma_no` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `sent_to_supplier` int(1) NOT NULL,
  `date_sent` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `rma_completed` int(1) NOT NULL,
  `created_by_id` int(11) NOT NULL,
  `note` text COLLATE latin1_general_ci NOT NULL,
  `date` varchar(25) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `rma_items`
--

CREATE TABLE `rma_items` (
  `id` int(11) NOT NULL,
  `rma_id` int(7) UNSIGNED ZEROFILL NOT NULL,
  `upc` int(6) UNSIGNED ZEROFILL NOT NULL,
  `quantity` int(11) NOT NULL,
  `rma_number` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `reason` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `return_action` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `rejected_rcvd_back` int(1) NOT NULL,
  `rejected_rcvd_back_date` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `rejected_rcvd_note` text COLLATE latin1_general_ci NOT NULL,
  `replaced_rcvd_back` int(1) NOT NULL,
  `replaced_rcvd_back_date` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `replaced_rcvd_note` text COLLATE latin1_general_ci NOT NULL,
  `credit_note_id` int(6) UNSIGNED ZEROFILL NOT NULL,
  `upgrade_reason` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `upgrade_new_upc` int(6) UNSIGNED ZEROFILL NOT NULL,
  `upgrade_paid_extra` int(1) NOT NULL,
  `upgrade_paid_amount` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `upgrade_rcvd_back` int(1) NOT NULL,
  `upgrade_rcvd_back_date` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `upgrade_rcvd_note` text COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rma_item_reasons`
--

CREATE TABLE `rma_item_reasons` (
  `id` int(11) NOT NULL,
  `description` text COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `rma_item_reasons`
--

INSERT INTO `rma_item_reasons` (`id`, `description`) VALUES
(1, 'BENT PIN IN CARD READER'),
(2, 'Blue Screens in Windows'),
(3, 'Blurry Image'),
(4, 'Brand New for Credit'),
(5, 'Breaking / Crackling Sound'),
(6, 'Broken'),
(7, 'Broken - Tray Does Not Close'),
(8, 'BSOD During Windows Installation'),
(9, 'BUZZING SOUND'),
(10, 'CAME WITH SCREW MISSING'),
(11, 'Cannot be detected'),
(12, 'Cannot Read / Burn CDs / DVDs'),
(13, 'Cannot Read CF Cards'),
(14, 'Cannot read data'),
(15, 'CANNOT READ SD CARDS'),
(16, 'Damaged'),
(17, 'DROPPING CONNECTION'),
(18, 'Ethernet port faulty'),
(19, 'EXTREMELY SLOW POST'),
(20, 'Fails Overclocking'),
(21, 'Fan not spinning'),
(22, 'Faulty'),
(23, 'Faulty - CD- Freezing and Crashing'),
(24, 'Faulty - CD- Tray Not opening'),
(25, 'Faulty - Cpu - Core Fails in Prime 95'),
(26, 'Faulty - Cpu - Crashes / Overheating'),
(27, 'Faulty - Cpu - NO DISPLAY'),
(28, 'Faulty - Cpu - NO POST'),
(29, 'Faulty - Cpu - Slightly Bent When Seated'),
(30, 'Faulty - Cpu - Slightly Bent When Seated and Screw Missing'),
(31, 'Faulty - FAN - DAMAGED BLADES'),
(32, 'Faulty - FAN - Heatsink Attachment Broken'),
(33, 'Faulty - FAN - LEAK'),
(34, 'Faulty - FAN - NO POST, KEEPS RESTARTING'),
(35, 'Faulty - Gfx - Blue Screen During Windows'),
(36, 'Faulty - Gfx - Crashes when Graphic Drivers Installed'),
(37, 'Faulty - Gfx - Distorted Display'),
(38, 'Faulty - Gfx - DVI Port No Display'),
(39, 'Faulty - Gfx - Fan stopping and starting intermittently'),
(40, 'Faulty - Gfx - Freezes During Benchmark'),
(41, 'Faulty - Gfx - Green Display'),
(42, 'Faulty - Gfx - Intermittant Display'),
(43, 'Faulty - Gfx - Long Beep Noise'),
(44, 'Faulty - Gfx - Loses Display during Unigene After 15 mins'),
(45, 'Faulty - Gfx - No Display'),
(46, 'Faulty - Gfx - No Display After Installing Drivers'),
(47, 'Faulty - Gfx - Overheating'),
(48, 'Faulty - Gfx - Rattling Fan'),
(49, 'Faulty - Gfx - Red Screen During Windows'),
(50, 'Faulty - Gfx - Very Noisy Fan'),
(51, 'Faulty - Hdd - Bad sector'),
(52, 'Faulty - Hdd - Blue screen'),
(53, 'Faulty - Hdd - Cannot be detected'),
(54, 'Faulty - Hdd - Cannot Install Windows'),
(55, 'Faulty - Hdd - Crashes In Testing (SEATOOLS)'),
(56, 'Faulty - Hdd - Creates Unwanted Partitions'),
(57, 'Faulty - Hdd - Error in BIOS, SMART, not booting up'),
(58, 'Faulty - Hdd - Freezing'),
(59, 'Faulty - Hdd - Rattling Noise'),
(60, 'Faulty - Headphone/ Speakers - Mic isnt working'),
(61, 'Faulty - Intermittent Display / Post'),
(62, 'Faulty - Motherboard -  CPU Pin Damage'),
(63, 'Faulty - Motherboard -  DAMAGED'),
(64, 'Faulty - Motherboard - 4 PIN power cannot be connected'),
(65, 'Faulty - Motherboard - Auto On'),
(66, 'Faulty - Motherboard - Bottom Two USB Ports Not Working'),
(67, 'Faulty - Motherboard - Cannot Install Windows (Error on startup)'),
(68, 'Faulty - Motherboard - CPU lock is very loose'),
(69, 'Faulty - Motherboard - Freezing/Crashing in Windows'),
(70, 'Faulty - Motherboard - GFX Slot Broken'),
(71, 'Faulty - Motherboard - Loses Display'),
(72, 'Faulty - Motherboard - Loses Power'),
(73, 'Faulty - Motherboard - Memory Slot DAMAGED'),
(74, 'Faulty - Motherboard - Memory Slot Issue'),
(75, 'Faulty - Motherboard - Missing SATA connector'),
(76, 'Faulty - Motherboard - No Display'),
(77, 'Faulty - Motherboard - No Display On Nvidia 580'),
(78, 'Faulty - Motherboard - No Internet'),
(79, 'Faulty - Motherboard - No Post'),
(80, 'Faulty - Motherboard - No Power'),
(81, 'Faulty - Motherboard - Not Shutting Down'),
(82, 'Faulty - Motherboard - Over Voltage'),
(83, 'Faulty - Motherboard - Overheating'),
(84, 'Faulty - Motherboard - Pci Slot Cannot Recognize'),
(85, 'Faulty - Motherboard - PCI-e Slot 1 Cannot Recognise GFX'),
(86, 'Faulty - Motherboard - Sata port not working'),
(87, 'Faulty - Motherboard - Unable to load Windows after ATI drivers have been installed'),
(88, 'Faulty - Psu - Broken Fan'),
(89, 'Faulty - Psu - Computer turn on when psu switched on'),
(90, 'Faulty - Psu - Damaged Cables'),
(91, 'Faulty - Psu - Extra Power on 5V'),
(92, 'Faulty - Psu - Loses Power'),
(93, 'Faulty - Psu - No Power'),
(94, 'Faulty - Psu - No Power on PCI-E connector'),
(95, 'Faulty - Psu - Noise'),
(96, 'Faulty - Psu - Not enough power'),
(97, 'Faulty - Psu - PSU Blew'),
(98, 'Faulty - Snd - Cannot be detected'),
(99, 'Faulty - Snd - Cannot Install Drivers'),
(100, 'Faulty - Snd - Mobo sound making buzzing noise'),
(101, 'Faulty - Snd - No Sound'),
(102, 'Faulty - SSD'),
(103, 'Freezing During 3D and PC Marks'),
(104, 'Freezing/Core Fails'),
(105, 'Front LED Not Working'),
(106, 'Front Panel Damaged'),
(107, 'Internal pin bent'),
(108, 'Loose connection'),
(109, 'Mem Test Fails'),
(110, 'Memory/PCI Slot Lock Problem'),
(111, 'Missing Screws'),
(112, 'No Front Sound'),
(113, 'No Post, No Display'),
(114, 'NO POWER'),
(115, 'No sound from 1 of the speakers'),
(116, 'Not Detecting the KVM switch, Cannot Use Two Computers'),
(117, 'Ordered in Error / Un Needed'),
(118, 'Pin Damage'),
(119, 'Received Damage'),
(120, 'Restarting in Loop'),
(121, 'Sent From Supplier in Error'),
(122, 'Short Circuiting from reset button'),
(123, 'Showing Only 2GB'),
(124, 'Side Panel Damaged'),
(125, 'Sound Card Disappear from Computer'),
(126, 'Sub Is Not Working'),
(127, 'Very Noisy/Ratling Noise');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(55) NOT NULL,
  `description` varchar(255) NOT NULL,
  `cost` varchar(10) NOT NULL,
  `created_by_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `cost`, `created_by_id`) VALUES
(1, 'OS Reinstallation', 'Operating System Reinstallation', '40.00', 1),
(2, 'Other', 'Other', '0.00', 1),
(3, 'Data Recovery', 'Data Recovery', '0.00', 1),
(4, 'Hardware Service & Cleaning', 'Hardware Service & Cleaning', '40.00', 1),
(5, 'Labour Charges', 'Labour Charges', '40.00', 1),
(6, 'Virus Removal', 'Virus Removal', '0.00', 1),
(7, 'Software & Security Installation', 'Software & Security Installation', '0.00', 1),
(8, 'Hinge Replacement', 'Hinge Replacement', '0.00', 1),
(9, 'Data Backup', 'Data Backup', '20.00', 1),
(10, 'Laptop Chasis Work', 'Laptop Chasis Work', '0.00', 1),
(11, 'Keyboard Replacement', 'Keyboard Replacement', '0.00', 1),
(12, 'Screen Replacement', 'Screen Replacement', '95.00', 1),
(13, 'iPad Touch Replacement', 'iPad Touch Replacement', '55.00', 1),
(14, 'Inspection Charges', 'Inspection Charges', '0.00', 1),
(15, 'Graphic Repairs (Reballing)', 'Graphic Repairs (Reballing)', '55.00', 1),
(16, 'Motherboard Chemical Wash', 'Motherboard Chemical Wash', '45.00', 1),
(17, 'DC Jack (Charging Port Replacement)', 'DC Jack (Charging Port Replacement)', '60.00', 1),
(18, 'SSD / HDD Replacement', 'SSD / HDD Replacement', '0.00', 1),
(19, 'Power Supply Replacement', 'Power Supply Replacement', '0.00', 1),
(20, 'RAM (Memory) Replacement', 'RAM (Memory) Replacement', '0.00', 1),
(21, 'Overclocking', 'Overclocking', '50.00', 1),
(22, 'CPU Fan Replacement / Cleaning', 'CPU Fan Replacement / Cleaning', '45.00', 1),
(23, 'Motherboard / Graphic Service', 'Motherboard / Graphic Service', '60.00', 1),
(24, 'Palmrest / Mouse Pad Repair', 'Palmrest / Mouse Pad Repair', '30.00', 2),
(25, 'Battery Replacement', 'Battery Replacement', '0.00', 2),
(26, 'Display Cable Replacement', 'Display Cable Replacement', '0.00', 2),
(27, 'DVD / Blu Ray Replacement', 'DVD / Blu Ray Replacement', '0.00', 2),
(28, 'On-Site Visit ( 1 hour )', 'On-Site Visit ( 1 hour )', '35.00', 1),
(29, 'iPhone Touch Replacement', 'iPhone Touch Replacement', '55.00', 2);

-- --------------------------------------------------------

--
-- Table structure for table `slider`
--

CREATE TABLE `slider` (
  `id` int(11) NOT NULL,
  `caption` text COLLATE latin1_general_ci NOT NULL,
  `sort` int(2) NOT NULL,
  `link` tinyint(1) NOT NULL,
  `url` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `image` varchar(255) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_logs`
--

CREATE TABLE `sms_logs` (
  `id` int(11) NOT NULL,
  `message_id` int(8) NOT NULL,
  `phone_number` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `message` text COLLATE latin1_general_ci NOT NULL,
  `sent_by_id` int(11) NOT NULL,
  `retries` int(2) NOT NULL,
  `status` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `time_sent` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `time_responded` varchar(10) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `sms_templates`
--

CREATE TABLE `sms_templates` (
  `id` int(11) NOT NULL,
  `type` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `description` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `content` text COLLATE latin1_general_ci NOT NULL,
  `created_by_id` int(11) NOT NULL,
  `last_updated_by_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `sms_templates`
--

INSERT INTO `sms_templates` (`id`, `type`, `description`, `content`, `created_by_id`, `last_updated_by_id`) VALUES
(1, 'receipt', 'Laptop Ready for Collection', 'Dear FIRST_LAST, We are pleased to inform that your laptop is now ready for collection. We are open Mon to Fri 10AM - 6PM, Sat 10AM - 2PM.', 1, 1),
(2, 'order', 'Ready for Collection', 'Dear FIRST_LAST, We are pleased to inform that your PC is now ready for collection. We are open Mon to Fri 10AM - 6PM, Sat 10AM - 2PM. Regards, Sydenham Support', 1, 2),
(3, 'receipt', 'Laptop not fixable', 'Dear FIRST_LAST, many thanks for bringing your laptop. We are sorry to let you know that we are unable to fix your laptop and we advise you to collect it.', 1, 1),
(4, 'receipt', 'PC Ready for Collection', 'Dear FIRST_LAST, We are pleased to inform that your PC is now ready for collection. We are open Mon to Fri 10AM - 6PM, Sat 10AM - 2PM.', 1, 1),
(5, 'order', 'Laptop Ready for Collection', 'Dear FIRST_LAST, We are pleased to inform that your laptop is now ready for collection. We are open Mon to Fri 10AM - 6PM, Sat 10AM - 2PM.', 1, 1),
(6, 'order', 'Parts ready for collection', 'Dear FIRST_LAST, We are pleased to inform that your parts are now ready for collection. We are open Mon to Fri 10AM - 6PM, Sat 10AM - 2PM.', 1, 1),
(7, 'receipt', 'Contact us', 'Dear FIRST_LAST, many thanks for bringing your PC/Laptop for inspection. Please contact us regarding repairs on 0208 778 0090', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `stock_cat`
--

CREATE TABLE `stock_cat` (
  `cat_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_by_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `stock_cat`
--

INSERT INTO `stock_cat` (`cat_id`, `description`, `created_by_id`) VALUES
(1, 'Screen / Monitors', 1),
(42, 'Laptop Miscellaneous Parts', 1),
(43, 'Miscellaneous', 1),
(44, 'PC Hard Drive', 1),
(45, 'Laptop Hard Drive', 1),
(46, 'CPU', 1),
(47, 'Intel Motherboards', 1),
(48, 'AMD Motherboards', 1),
(49, 'Intel Laptop Motherboards', 1),
(50, 'AMD Laptop Motherboards', 1),
(51, 'Cases', 1),
(52, 'Desktop RAM', 1),
(53, 'Laptop RAM', 1),
(54, 'External Storage', 1),
(55, 'Operating Systems', 1),
(56, 'Networking', 1),
(57, 'Software', 1),
(58, 'Solid State Drives', 1),
(59, 'Optical Drives', 1),
(60, 'Peripherals', 1),
(61, 'Graphics Cards', 1),
(62, 'Power Supplies', 2),
(63, 'Laptops', 2),
(65, 'Laptop Replacement Chargers', 1),
(66, 'Card Readers', 1),
(67, 'PCI / PCI-E Cards', 2),
(68, 'Laptop Replacement Batteries', 1),
(69, 'Laptop Replacement Keyboards', 1),
(70, 'Laptop Replacement Charging Ports', 1),
(71, 'NUC / Media PC', 1),
(72, 'Cooling', 1),
(73, 'Tablets / Mobiles/All in One', 1);

-- --------------------------------------------------------

--
-- Table structure for table `stock_items`
--

CREATE TABLE `stock_items` (
  `upc` int(6) UNSIGNED ZEROFILL NOT NULL,
  `description` varchar(255) NOT NULL,
  `public` varchar(255) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `subcat_id` int(11) NOT NULL,
  `created_by_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Table structure for table `stock_subcat`
--

CREATE TABLE `stock_subcat` (
  `subcat_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `created_by_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `stock_subcat`
--

INSERT INTO `stock_subcat` (`subcat_id`, `description`, `cat_id`, `created_by_id`) VALUES
(1, '18.5 LED Monitor', 1, 1),
(2, '19.5 LED Monitor', 1, 1),
(3, '21.5 LED Monitor', 1, 1),
(4, '23.5 LED Monitor', 1, 1),
(5, '24 LED Monitor', 1, 1),
(6, '27 LED Monitor', 1, 1),
(7, '9.7 Netbook Replacement Screen', 1, 1),
(8, '10.1 Netbook Replacement Screen', 1, 1),
(9, '12.1 Notebook Replacement Screen', 1, 1),
(10, '13.3 Notebook Replacement Screen', 1, 1),
(11, '14.0 Notebook Replacement Screen', 1, 1),
(12, '15.4 Notebook Replacement Screen', 1, 1),
(13, '15.6 Notebook Replacement Screen', 1, 1),
(14, '15.6 Notebook Replacement LCD Screen', 1, 1),
(15, '15.6 Notebook Replacement LED Screen', 1, 1),
(16, '15.6 Notebook Replacement LED Slim Screen', 1, 1),
(17, '16.1 Notebook Replacement Screen', 1, 1),
(18, '17.3 Notebook Replacement Screen', 1, 1),
(88, 'Screen Bezel', 42, 1),
(89, 'Webcam', 42, 1),
(90, 'Replacement USB Ports', 42, 1),
(91, 'Base Cover', 42, 1),
(92, 'Top Lid', 42, 1),
(93, 'Screen Cable', 42, 1),
(94, 'Screen Converter Cable', 42, 1),
(95, 'Palmrest', 42, 1),
(96, 'Cooling Fan', 42, 1),
(98, 'Power Cables', 43, 1),
(99, 'Display Cables', 43, 1),
(100, 'Enclosures / Caddy', 44, 1),
(101, 'Enclosures / Caddy', 45, 1),
(102, 'SATA Hard Drive', 45, 1),
(104, 'SATA Hard Drive', 44, 1),
(105, 'Intel Core Haswell', 46, 1),
(106, 'AMD FM2 / FM2+', 46, 1),
(107, 'Intel Socket 775', 47, 1),
(108, 'Intel Socket 1155', 47, 1),
(109, 'Intel Socket 1150', 47, 1),
(110, 'Intel Socket 2011', 47, 1),
(111, 'Intel Socket 2011-3', 47, 1),
(112, 'AM3 / AM3+', 48, 1),
(113, 'FM2 / FM2+', 48, 1),
(114, 'FM1', 48, 1),
(115, 'AM1', 48, 1),
(116, 'Intel Laptop Motherboards', 49, 1),
(117, 'AMD Laptop Motherboards', 50, 1),
(118, 'Generic Mid-Tower', 51, 1),
(119, 'Generic Micro-Tower', 51, 1),
(120, 'Aerocool', 51, 1),
(121, 'Antec', 51, 1),
(122, 'Cooler Master', 51, 1),
(123, 'Fractal Design', 51, 1),
(124, 'INWIN', 51, 1),
(125, 'NZXT', 51, 1),
(126, 'Sharkoon', 51, 1),
(127, 'Silverstone', 51, 1),
(128, 'ThermalTake', 51, 1),
(129, 'Zalman', 51, 1),
(130, 'DDR', 52, 1),
(131, 'DDR2', 52, 1),
(132, 'DDR3', 52, 1),
(133, 'DDR4', 52, 1),
(134, 'DDR SODIMM', 53, 1),
(135, 'DDR2 SODIMM', 53, 1),
(136, 'DDR3 SODIMM', 53, 1),
(137, 'DDR4 SODIMM', 53, 1),
(138, '2.5 inch Portable drive', 54, 1),
(139, '3.5 inch Standalone drive', 54, 1),
(140, 'NAS Drive', 54, 1),
(141, 'Flash Drive', 54, 1),
(142, 'Windows 7', 55, 1),
(143, 'Windows 8', 55, 1),
(144, 'Broadband Modems / Routers / Cables', 56, 1),
(145, 'External USB Wireless Adaptors', 56, 1),
(146, 'Internal Wireless Network Cards', 56, 1),
(147, 'Lan Cards', 56, 1),
(148, 'Modems', 56, 1),
(149, 'Antivirus', 57, 1),
(150, 'Multimedia', 57, 1),
(151, 'Microsoft Office', 57, 1),
(152, 'Other', 57, 1),
(153, 'Solid State Drives', 58, 1),
(154, 'Laptop DVD Drives', 59, 1),
(155, 'PC DVD Drives', 59, 1),
(156, 'Wired Keyboard', 60, 1),
(157, 'Wireless Keyboard', 60, 1),
(158, 'Wireless Mouse', 60, 1),
(159, 'Wired Mouse', 60, 1),
(160, 'Wired Keyboard & Mouse Combo', 60, 1),
(161, 'Wireless Keyboard & Mouse Combo', 60, 1),
(162, 'AMD AM3 / AM3+', 46, 1),
(163, 'AMD Radeon Cards', 61, 1),
(164, 'nVidia GeForce Cards', 61, 1),
(165, 'nVidia Quadro / Pro Cards', 61, 1),
(166, 'AMD FireGL / Pro Cards', 61, 1),
(167, 'Miscellaneous', 43, 1),
(168, 'Laptop VGA Cables', 43, 1),
(169, '0W - 500W', 62, 2),
(170, '501W - 750W', 62, 2),
(171, '751W - 1500W', 62, 2),
(172, 'Speakers', 60, 2),
(173, 'Webcams', 60, 2),
(177, 'Refurbished Laptops', 63, 2),
(178, 'New Laptops', 63, 2),
(179, 'External DVD Drives', 59, 1),
(180, 'Flash Memory Cards', 54, 1),
(181, 'Headphones', 60, 1),
(182, 'Network Cables', 43, 2),
(183, 'Intel Sandy / IVY Bridge', 46, 1),
(184, 'Apple', 65, 1),
(185, '11.6 Notebook Replacement Screen', 1, 1),
(187, 'Fujitsu / Linshi', 65, 1),
(188, 'HP / Compaq', 65, 1),
(189, 'Acer / Packard Bell / Advent / Medion', 65, 1),
(190, 'Asus', 65, 1),
(191, 'Dell', 65, 1),
(192, 'IBM / Lenovo', 65, 1),
(193, 'Miscellaneous / Universal', 65, 1),
(194, 'Samsung', 65, 1),
(195, 'Sony', 65, 1),
(196, 'Toshiba', 65, 1),
(197, 'External Card Readers', 66, 1),
(198, 'Internal Card Readers', 66, 1),
(199, 'USB 3.0 / SATA 6 Cards', 67, 2),
(200, 'Fujitsu / Linshi', 68, 1),
(201, 'HP / Compaq', 68, 1),
(202, 'Acer / Packard Bell / Advent / Medion', 68, 1),
(203, 'Asus', 68, 1),
(204, 'Dell', 68, 1),
(205, 'IBM / Lenovo', 68, 1),
(206, 'Miscellaneous / Universal', 68, 1),
(207, 'Samsung', 68, 1),
(208, 'Sony', 68, 1),
(209, 'Toshiba', 68, 1),
(210, 'Fujitsu / Linshi', 69, 1),
(211, 'HP / Compaq', 69, 1),
(212, 'Acer / Packard Bell / Advent / Medion', 69, 1),
(213, 'Asus', 69, 1),
(214, 'Dell', 69, 1),
(215, 'IBM / Lenovo', 69, 1),
(216, 'Miscellaneous / Universal', 69, 1),
(217, 'Samsung', 69, 1),
(218, 'Sony', 69, 1),
(219, 'Toshiba', 69, 1),
(221, 'Fujitsu / Linshi', 70, 1),
(222, 'HP / Compaq', 70, 1),
(223, 'Acer / Packard Bell / Advent / Medion', 70, 1),
(224, 'Asus', 70, 1),
(225, 'Dell', 70, 1),
(226, 'IBM / Lenovo', 70, 1),
(227, 'Miscellaneous / Universal', 70, 1),
(228, 'Samsung', 70, 1),
(229, 'Sony', 70, 1),
(230, 'Toshiba', 70, 1),
(231, 'Laptop Hinges', 42, 1),
(232, 'Intel i3 NUC Barebone', 71, 1),
(233, 'CPU Coolers', 72, 1),
(234, '12.5 Notebook Replacement Screen', 1, 1),
(235, '12.5 Notebook Replacement Screen', 1, 1),
(236, 'Soundcards', 67, 1),
(237, 'USB Devices', 60, 1),
(238, 'Corsair', 51, 2),
(239, 'Case Fans', 72, 2),
(240, 'Intel Socket 1151', 47, 1),
(241, 'Intel Skylake', 46, 1),
(242, 'Windows 10', 55, 1),
(243, 'BluRay Drive', 59, 2),
(244, '4K/5K Monitors', 1, 2),
(245, 'Tablets', 73, 1),
(246, 'Mobiles', 73, 1),
(247, 'Accessories', 73, 1),
(248, '21.5\'\' Replacement Screens', 1, 1),
(249, 'AM2 / AM2+', 48, 1),
(250, 'Intel Broadwell 2011-3', 46, 2),
(251, 'Intel Kabylake ', 46, 2),
(252, 'Server Case', 51, 2),
(253, 'Cooling/Thermal Paste', 72, 2),
(254, 'Intel Coffee Lake', 46, 2),
(255, 'AMD AM4 Series', 46, 2),
(256, 'AMD AM4 Series', 48, 2),
(257, 'All in One', 73, 2),
(258, 'Intel Socket 2066', 47, 2),
(259, 'Intel CPU 2066', 46, 2);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `contact_name` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `contact_number` varchar(32) COLLATE latin1_general_ci NOT NULL,
  `contact_email` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `web_account_username` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `web_account_password` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `website` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created_by_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `todo`
--

CREATE TABLE `todo` (
  `id` int(11) NOT NULL,
  `added` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `do_by` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `do_by_time` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `content` text COLLATE latin1_general_ci NOT NULL,
  `done` tinyint(1) NOT NULL,
  `done_on` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `extended` int(3) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dismissed` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `salt` varchar(10) NOT NULL,
  `level` int(1) NOT NULL,
  `email` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `surname` varchar(32) NOT NULL,
  `skype` varchar(32) NOT NULL,
  `last_action_time` varchar(25) NOT NULL,
  `phone_number` varchar(25) NOT NULL,
  `created_by_id` int(11) NOT NULL,
  `mark_attendance` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `salt`, `level`, `email`, `name`, `surname`, `skype`, `last_action_time`, `phone_number`, `created_by_id`, `mark_attendance`, `active`) VALUES
(1, 'demo', '600ebda797031dca7341b5da88ac302e', 'yJAVU$Y9U2', 1, 'mikszvirbulis@yahoo.com', 'Demo', 'Account', 'noskype', '1548706333', '07111111111', 1, 0, 1);

--
-- Table structure for table `wages`
--

CREATE TABLE `wages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `by` varchar(55) COLLATE latin1_general_ci NOT NULL,
  `date` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `amount` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `reason` text COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warranty_options`
--

CREATE TABLE `warranty_options` (
  `id` int(11) NOT NULL,
  `length` varchar(2) COLLATE latin1_general_ci NOT NULL,
  `description` text COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `warranty_options`
--

INSERT INTO `warranty_options` (`id`, `length`, `description`) VALUES
(1, '1m', '1 month ( Return to Base, Parts & Labour )'),
(2, '3m', '3 months ( Return to Base, Parts & Labour )'),
(3, '6m', '6 months ( Return to Base, Parts & Labour )'),
(4, '1y', '1 year ( Return to Base, Parts & Labour )'),
(5, '2y', '2 years ( Return to Base, 1rst year Parts & Labour, 2nd year Labour only )'),
(6, '2y', '2 years ( Return to Base, Parts & Labour )'),
(8, 'na', 'No Warranty'),
(9, '1y', '1 year ( Return to Base, Parts )'),
(10, '1m', '1 month ( Return to Base, Parts )'),
(11, '3m', '3 months ( Return to Base, Parts )'),
(12, '6m', '6 months ( Return to Base, Parts )'),
(13, '2y', '2 years ( Direct Manufacturer Warranty )'),
(14, '1y', '1 year ( Direct Manufacturer Warranty )'),
(15, '3y', '3 years ( Direct Manufacturer Warranty )'),
(16, '2m', '2 months ( Return to Base, Parts )');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `credit_notes`
--
ALTER TABLE `credit_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `credit_note_items`
--
ALTER TABLE `credit_note_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_methods`
--
ALTER TABLE `delivery_methods`
  ADD PRIMARY KEY (`delivery_key`);

--
-- Indexes for table `email_categories`
--
ALTER TABLE `email_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_number`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `opening_stock`
--
ALTER TABLE `opening_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `order_notes`
--
ALTER TABLE `order_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_specs`
--
ALTER TABLE `order_specs`
  ADD PRIMARY KEY (`spec_id`);

--
-- Indexes for table `payment_entries`
--
ALTER TABLE `payment_entries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`purchase_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `receipt_items`
--
ALTER TABLE `receipt_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rma`
--
ALTER TABLE `rma`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rma_items`
--
ALTER TABLE `rma_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rma_item_reasons`
--
ALTER TABLE `rma_item_reasons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_logs`
--
ALTER TABLE `sms_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_templates`
--
ALTER TABLE `sms_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_cat`
--
ALTER TABLE `stock_cat`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `stock_items`
--
ALTER TABLE `stock_items`
  ADD PRIMARY KEY (`upc`);

--
-- Indexes for table `stock_subcat`
--
ALTER TABLE `stock_subcat`
  ADD PRIMARY KEY (`subcat_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `todo`
--
ALTER TABLE `todo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wages`
--
ALTER TABLE `wages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `warranty_options`
--
ALTER TABLE `warranty_options`
  ADD PRIMARY KEY (`id`);