SET foreign_key_checks = 0;

TRUNCATE TABLE `category`;
TRUNCATE TABLE `task_sub_category`;
TRUNCATE TABLE `ref_data`;
TRUNCATE TABLE `country`;
TRUNCATE TABLE `submission_section`;
TRUNCATE TABLE `conviction_category`;
TRUNCATE TABLE `document_sub_category`;

INSERT INTO `country` (`id`,`country_desc`) VALUES
    ('GB', 'United Kingdom'),
    ('AF', 'Afghanistan'),
    ('AX', 'Aland Islands'),
    ('AL', 'Albania'),
    ('DZ', 'Algeria'),
    ('AS', 'American Samoa'),
    ('AD', 'Andorra'),
    ('AO', 'Angola'),
    ('AI', 'Anguilla'),
    ('AQ', 'Antarctica'),
    ('AG', 'Antigua And Barbuda'),
    ('AR', 'Argentina'),
    ('AM', 'Armenia'),
    ('AW', 'Aruba'),
    ('AU', 'Australia'),
    ('AT', 'Austria'),
    ('AZ', 'Azerbaijan'),
    ('BS', 'Bahamas'),
    ('BH', 'Bahrain'),
    ('BD', 'Bangladesh'),
    ('BB', 'Barbados'),
    ('BY', 'Belarus'),
    ('BE', 'Belgium'),
    ('BZ', 'Belize'),
    ('BJ', 'Benin'),
    ('BM', 'Bermuda'),
    ('BT', 'Bhutan'),
    ('BO', 'Bolivia'),
    ('BA', 'Bosnia And Herzegovina'),
    ('BW', 'Botswana'),
    ('BV', 'Bouvet Island'),
    ('BR', 'Brazil'),
    ('IO', 'British Indian Ocean Territory'),
    ('BN', 'Brunei Darussalam'),
    ('BG', 'Bulgaria'),
    ('BF', 'Burkina Faso'),
    ('BI', 'Burundi'),
    ('KH', 'Cambodia'),
    ('CM', 'Cameroon'),
    ('CA', 'Canada'),
    ('CV', 'Cape Verde'),
    ('KY', 'Cayman Islands'),
    ('CF', 'Central African Republic'),
    ('TD', 'Chad'),
    ('CL', 'Chile'),
    ('CN', 'China'),
    ('CX', 'Christmas Island'),
    ('CC', 'Cocos (Keeling) Islands'),
    ('CO', 'Colombia'),
    ('KM', 'Comoros'),
    ('CG', 'Congo'),
    ('CD', 'Congo, Democratic Republic'),
    ('CK', 'Cook Islands'),
    ('CR', 'Costa Rica'),
    ('CI', 'Cote D\'Ivoire'),
    ('HR', 'Croatia'),
    ('CU', 'Cuba'),
    ('CY', 'Cyprus'),
    ('CZ', 'Czech Republic'),
    ('DK', 'Denmark'),
    ('DJ', 'Djibouti'),
    ('DM', 'Dominica'),
    ('DO', 'Dominican Republic'),
    ('EC', 'Ecuador'),
    ('EG', 'Egypt'),
    ('SV', 'El Salvador'),
    ('GQ', 'Equatorial Guinea'),
    ('ER', 'Eritrea'),
    ('EE', 'Estonia'),
    ('ET', 'Ethiopia'),
    ('FK', 'Falkland Islands (Malvinas)'),
    ('FO', 'Faroe Islands'),
    ('FJ', 'Fiji'),
    ('FI', 'Finland'),
    ('FR', 'France'),
    ('GF', 'French Guiana'),
    ('PF', 'French Polynesia'),
    ('TF', 'French Southern Territories'),
    ('GA', 'Gabon'),
    ('GM', 'Gambia'),
    ('GE', 'Georgia'),
    ('DE', 'Germany'),
    ('GH', 'Ghana'),
    ('GI', 'Gibraltar'),
    ('GR', 'Greece'),
    ('GL', 'Greenland'),
    ('GD', 'Grenada'),
    ('GP', 'Guadeloupe'),
    ('GU', 'Guam'),
    ('GT', 'Guatemala'),
    ('GG', 'Guernsey'),
    ('GN', 'Guinea'),
    ('GW', 'Guinea-Bissau'),
    ('GY', 'Guyana'),
    ('HT', 'Haiti'),
    ('HM', 'Heard Island & Mcdonald Islands'),
    ('VA', 'Holy See (Vatican City State)'),
    ('HN', 'Honduras'),
    ('HK', 'Hong Kong'),
    ('HU', 'Hungary'),
    ('IS', 'Iceland'),
    ('IN', 'India'),
    ('ID', 'Indonesia'),
    ('IR', 'Iran, Islamic Republic Of'),
    ('IQ', 'Iraq'),
    ('IE', 'Ireland'),
    ('IM', 'Isle Of Man'),
    ('IL', 'Israel'),
    ('IT', 'Italy'),
    ('JM', 'Jamaica'),
    ('JP', 'Japan'),
    ('JE', 'Jersey'),
    ('JO', 'Jordan'),
    ('KZ', 'Kazakhstan'),
    ('KE', 'Kenya'),
    ('KI', 'Kiribati'),
    ('KR', 'Korea'),
    ('KW', 'Kuwait'),
    ('KG', 'Kyrgyzstan'),
    ('LA', 'Lao People\'s Democratic Republic'),
    ('LV', 'Latvia'),
    ('LB', 'Lebanon'),
    ('LS', 'Lesotho'),
    ('LR', 'Liberia'),
    ('LY', 'Libyan Arab Jamahiriya'),
    ('LI', 'Liechtenstein'),
    ('LT', 'Lithuania'),
    ('LU', 'Luxembourg'),
    ('MO', 'Macao'),
    ('MK', 'Macedonia'),
    ('MG', 'Madagascar'),
    ('MW', 'Malawi'),
    ('MY', 'Malaysia'),
    ('MV', 'Maldives'),
    ('ML', 'Mali'),
    ('MT', 'Malta'),
    ('MH', 'Marshall Islands'),
    ('MQ', 'Martinique'),
    ('MR', 'Mauritania'),
    ('MU', 'Mauritius'),
    ('YT', 'Mayotte'),
    ('MX', 'Mexico'),
    ('FM', 'Micronesia, Federated States Of'),
    ('MD', 'Moldova'),
    ('MC', 'Monaco'),
    ('MN', 'Mongolia'),
    ('ME', 'Montenegro'),
    ('MS', 'Montserrat'),
    ('MA', 'Morocco'),
    ('MZ', 'Mozambique'),
    ('MM', 'Myanmar'),
    ('NA', 'Namibia'),
    ('NR', 'Nauru'),
    ('NP', 'Nepal'),
    ('NL', 'Netherlands'),
    ('AN', 'Netherlands Antilles'),
    ('NC', 'New Caledonia'),
    ('NZ', 'New Zealand'),
    ('NI', 'Nicaragua'),
    ('NE', 'Niger'),
    ('NG', 'Nigeria'),
    ('NU', 'Niue'),
    ('NF', 'Norfolk Island'),
    ('MP', 'Northern Mariana Islands'),
    ('NO', 'Norway'),
    ('OM', 'Oman'),
    ('PK', 'Pakistan'),
    ('PW', 'Palau'),
    ('PS', 'Palestinian Territory, Occupied'),
    ('PA', 'Panama'),
    ('PG', 'Papua New Guinea'),
    ('PY', 'Paraguay'),
    ('PE', 'Peru'),
    ('PH', 'Philippines'),
    ('PN', 'Pitcairn'),
    ('PL', 'Poland'),
    ('PT', 'Portugal'),
    ('PR', 'Puerto Rico'),
    ('QA', 'Qatar'),
    ('RE', 'Reunion'),
    ('RO', 'Romania'),
    ('RU', 'Russian Federation'),
    ('RW', 'Rwanda'),
    ('BL', 'Saint Barthelemy'),
    ('SH', 'Saint Helena'),
    ('KN', 'Saint Kitts And Nevis'),
    ('LC', 'Saint Lucia'),
    ('MF', 'Saint Martin'),
    ('PM', 'Saint Pierre And Miquelon'),
    ('VC', 'Saint Vincent And Grenadines'),
    ('WS', 'Samoa'),
    ('SM', 'San Marino'),
    ('ST', 'Sao Tome And Principe'),
    ('SA', 'Saudi Arabia'),
    ('SN', 'Senegal'),
    ('RS', 'Serbia'),
    ('SC', 'Seychelles'),
    ('SL', 'Sierra Leone'),
    ('SG', 'Singapore'),
    ('SK', 'Slovakia'),
    ('SI', 'Slovenia'),
    ('SB', 'Solomon Islands'),
    ('SO', 'Somalia'),
    ('ZA', 'South Africa'),
    ('GS', 'South Georgia And Sandwich Isl.'),
    ('ES', 'Spain'),
    ('LK', 'Sri Lanka'),
    ('SD', 'Sudan'),
    ('SR', 'Suriname'),
    ('SJ', 'Svalbard And Jan Mayen'),
    ('SZ', 'Swaziland'),
    ('SE', 'Sweden'),
    ('CH', 'Switzerland'),
    ('SY', 'Syrian Arab Republic'),
    ('TW', 'Taiwan'),
    ('TJ', 'Tajikistan'),
    ('TZ', 'Tanzania'),
    ('TH', 'Thailand'),
    ('TL', 'Timor-Leste'),
    ('TG', 'Togo'),
    ('TK', 'Tokelau'),
    ('TO', 'Tonga'),
    ('TT', 'Trinidad And Tobago'),
    ('TN', 'Tunisia'),
    ('TR', 'Turkey'),
    ('TM', 'Turkmenistan'),
    ('TC', 'Turks And Caicos Islands'),
    ('TV', 'Tuvalu'),
    ('UG', 'Uganda'),
    ('UA', 'Ukraine'),
    ('AE', 'United Arab Emirates'),
    ('US', 'United States'),
    ('UM', 'United States Outlying Islands'),
    ('UY', 'Uruguay'),
    ('UZ', 'Uzbekistan'),
    ('VU', 'Vanuatu'),
    ('VE', 'Venezuela'),
    ('VN', 'Viet Nam'),
    ('VG', 'Virgin Islands, British'),
    ('VI', 'Virgin Islands, U.S.'),
    ('WF', 'Wallis And Futuna'),
    ('EH', 'Western Sahara'),
    ('YE', 'Yemen'),
    ('ZM', 'Zambia'),
    ('ZW', 'Zimbabwe');


/* TODO : As part of ETL process DROP the olbs columns */

INSERT INTO ref_data(
  ref_data_category_id, id, description, olbs_key
)values
/* TL_TM_COMPLIANCE_EPISODE_APPEAL_OUTCOME */
  ('appeal_outcome', 'appeal_o_dis', 'Dismissed', 'DIS')
  , ('appeal_outcome', 'appeal_o_pas', 'Partially Successful', 'PAS')
  , ('appeal_outcome', 'appeal_o_ref', 'Refer Back to TC', 'REF')
  , ('appeal_outcome', 'appeal_o_suc', 'Successful', 'SUC')
/* TL_TM_COMPLIANCE_EPISODE_APPEAL_REASON */
  , ('appeal_reason', 'appeal_r_app', 'Application', '0')
  , ('appeal_reason', 'appeal_r_lic_pi', 'Disciplinary PI', 'Disciplinary_PI')
  , ('appeal_reason', 'appeal_r_lic_non_pi', 'Disciplinary Non-PI', 'Disciplinary_N_PI')
  , ('appeal_reason', 'appeal_r_tm_pi', 'Regulatory PI', '1')
  , ('appeal_reason', 'appeal_r_tm_non_pi', 'Regulatory Non-PI', '2')
/* TL_BUS_SUBSIDIES */
  , ('bus_subsidy','bs_no', 'No', '1')
  , ('bus_subsidy','bs_yes', 'Yes', '2')
  , ('bus_subsidy','bs_in_part', 'In Part', '3')
/* TL_BUS_TRC_STATUS */
/*
  , ('bus_trc_status', 'bts_new', 'New', null)
  , ('bus_trc_status', 'bts_valid', 'Valid', null)
  , ('bus_trc_status', 'bts_revoked', 'Revoked', null)
  , ('bus_trc_status', 'bts_refused', 'Refused', null)
  */
/* tl_episode_type */
  , ('erru_case_type', 'erru_case_t_msi', 'MSI', 'MSI')
  , ('erru_case_type', 'erru_case_t_msinre', 'MSI - No response entered', 'MSINRE')
  , ('erru_case_type', 'erru_case_t_msirnys', 'MSI - Response not sent yet', 'MSIRNYS')
  , ('erru_case_type', 'erru_case_t_nmsi', 'Non-MSI', 'NMSI')
/* case type - new in olcs */
  , ('case_type', 'case_t_app', 'Application', null)
  , ('case_type', 'case_t_lic', 'Licence', null)
  , ('case_type', 'case_t_tm', 'Transport Manager', null)
  /* TL_COMPLAINT_STATUS */
  , ('complaint_status', 'cs_ack', 'Acknowledged', 'ACK')
  , ('complaint_status', 'cs_pin', 'PI Notified', 'PIN')
  , ('complaint_status', 'cs_rfs', 'Review Form Sent', 'RFS')
  , ('complaint_status', 'cs_vfr', 'Valid For Review', 'VFR')
  , ('complaint_status', 'cs_yst', 'Are You Still There', 'YST')
/* TL_TYPE_OF_COMPLAINT */
  , ('complaint_type', 'ct_cor', 'Continuing to operator after Revocation', null)
  , ('complaint_type', 'ct_cov', 'Condition of Vehicles', null)
  , ('complaint_type', 'ct_dgm', 'Driving in a dangerous manner', null)
  , ('complaint_type', 'ct_dsk', 'Driver smoking', null)
  , ('complaint_type', 'ct_fls', 'Failure to operator local service', null)
  , ('complaint_type', 'ct_lvu', 'Leaving vehicle unattended with engine running', null)
  , ('complaint_type', 'ct_ndl', 'Not having correct category of Drivers Licence', null)
  , ('complaint_type', 'ct_nol', 'No Operators Licence', null)
  , ('complaint_type', 'ct_olr', 'Operating local service off route', null)
  , ('complaint_type', 'ct_ovb', 'Obstructing other vehicles at Bus Station/Bus Stop', null)
  , ('complaint_type', 'ct_pvo', 'Parking vehicle out with Operating Centre', null)
  , ('complaint_type', 'ct_rds', 'Registration of duplicate services', null)
  , ('complaint_type', 'ct_rta', 'Registered times not being adhered to', null)
  , ('complaint_type', 'ct_sln', 'Speed Limiters non operative', null)
  , ('complaint_type', 'ct_spe', 'Speeding', null)
  , ('complaint_type', 'ct_tgo', 'Tachograph offences', null)
  , ('complaint_type', 'ct_ufl', 'Unsafe loads', null)
  , ('complaint_type', 'ct_ump', 'Use of mobile phones while driving', null)
  , ('complaint_type', 'ct_urd', 'Using Red Diesel', null)
  , ('complaint_type', 'ct_vpo', 'Vehicles parked and causing an obstruction', null)
;
INSERT INTO ref_data(
  ref_data_category_id, id, description, olbs_key
)values
/* Condition Added Via */
  ('cond_added_via', 'cav_case', 'Case', 'Episode')
  , ('cond_added_via', 'cav_lic', 'Licence', 'Licence')
  , ('cond_added_via', 'cav_app', 'Application', 'Application')
/* Condition Attached To */
  , ('cond_att_to', 'cat_lic', 'Licence', 'Licence')
  , ('cond_att_to', 'cat_oc', 'Operating Centre', 'OpCentre')
/* TL_CONDITION_TYPE */
  , ('cond_type', 'cdt_con', 'Condition', '1')
  , ('cond_type', 'cdt_und', 'Undertaking', '2')
/* TL_CONTACT_METHOD */
  , ('contact_method', 'cm_letter', 'Letter', '1')
  , ('contact_method', 'cm_fax', 'Facsimile', '2')
  , ('contact_method', 'cm_email', 'E-mail', '3')
  , ('contact_method', 'cm_tel', 'Telephone', '4')
/* Contact Type */
  , ('contact_type', 'ct_est', 'Establishment', null)
  , ('contact_type', 'ct_reg', 'Registered', null)
  , ('contact_type', 'ct_tcon', 'Transport Consultant', null)
  , ('contact_type', 'ct_corr', 'Correspondence', null)
  , ('contact_type', 'ct_work','Workshop',null)
  , ('contact_type', 'ct_complainant', 'Complainant', null)
  , ('contact_type', 'ct_tm', 'Transport Manager', null)
  , ('contact_type', 'ct_ta', 'Traffic Area', null)
  , ('contact_type', 'ct_team_user', 'Team or User', null)
  , ('contact_type', 'ct_partner', 'Partner', null)
  , ('contact_type', 'ct_hackney', 'Hackney Licence Issue Council', null)
  , ('contact_type', 'ct_obj', 'Objector', null)
  , ('contact_type', 'ct_rep', 'Representor', null)
  , ('contact_type', 'ct_irfo_op', 'IRFO Operator', null)
  , ('contact_type', 'ct_driver', 'Driver', null)
/* TL_DISC_REMOVAL_EXPLANATION */
  , ('disc_removal_explan', 'dre_tao', 'Returned to TAO', '0')
  , ('disc_removal_explan', 'dre_lost', 'Lost', '1')
  , ('disc_removal_explan', 'dre_stolen', 'Stolen', '2')
  , ('disc_removal_explan', 'dre_destroyed', 'Destroyed', '3')
  ;

  INSERT INTO ref_data(
  ref_data_category_id, id, description, olbs_key
)values
/* Defendant types */
  ('def_type', 'def_t_op', 'Operator', null)
  , ('def_type', 'def_t_driver', 'Driver', null)
  , ('def_type', 'def_t_tm', 'Transport Manager', null)
  , ('def_type', 'def_t_dir', 'Director', null)
  , ('def_type', 'def_t_part', 'Partner', null)
  , ('def_type', 'def_t_owner', 'Owner', null)
/* Hearing Types */
/*
  , ('hearing_type', 'hearing_stl', 'STL Interview', 'AD_Interview')
  , ('hearing_type', 'hearing_prelim', 'Preliminary Hearing', 'IC_Meeting')
*/
/* Impounding Types */
  , ('impound_type', 'impt_hearing', 'Hearing', null)
  , ('impound_type', 'impt_paper', 'Paperwork', null)
/* Impounding Outcome */
  , ('impound_outcome', 'impo_returned', 'Vehicle Returned', null)
  , ('impound_outcome', 'impo_not', 'Vehicle Not Returned', null)
/* TL_INSP_REPOST_TYPE */
  , ('insp_report_type', 'insp_rep_t_maint', 'Maintenance Request', '1')
  , ('insp_report_type', 'insp_rep_t_TE', 'Traffic Examiner', '2')
  , ('insp_report_type', 'insp_rep_t_bus', 'Bus Monitor', '3')
/* irfo gv permit status - new */
  , ('irfo_permit_status', 'irfo_perm_s_refused', 'Refused', 'Refused')
  , ('irfo_permit_status', 'irfo_perm_s_pending', 'Pending', 'Pending')
  , ('irfo_permit_status', 'irfo_perm_s_withdrawn', 'Withdrawn', 'Withdrawn')
  , ('irfo_permit_status', 'irfo_perm_s_appreoved', 'Approved', 'Approved')
/* irfo_permit_stock.status */
  , ('irfo_permit_stock_status', 'irfo_perm_s_s_issued', 'Issued', 'Issued')
  , ('irfo_permit_stock_status', 'irfo_perm_s_s_ret', 'Returned', 'Returned')
  , ('irfo_permit_stock_status', 'irfo_perm_s_s_void', 'Void', 'Void')
  , ('irfo_permit_stock_status', 'irfo_perm_s_s_in_stock', 'In Stock', 'In Stock')
/* irfo psv journey frequency */
  , ('irfo_psv_journey_freq', 'psv_freq_other', 'OTHER', 'OTHER')
  , ('irfo_psv_journey_freq', 'psv_freq_weekly', 'WEEKLY', 'WEEKLY')
  , ('irfo_psv_journey_freq', 'psv_freq_daily', 'DAILY', 'DAILY')
  , ('irfo_psv_journey_freq', 'psv_freq_fortnight', 'FORTNIGHTLY', 'FORTNIGHTLY')
  , ('irfo_psv_journey_freq', 'psv_freq_monthly', 'MONTHLY', 'MONTHLY')
  , ('irfo_psv_journey_freq', 'psv_freq_2_weekly', 'TWICE WEEKLY', 'TWICE WEEKLY')
/* irfo psv auth status - new */
  , ('irfo_auth_status', 'irfo_auth_s_approved', 'Approved', 'Approved')
  , ('irfo_auth_status', 'irfo_auth_s_cns', 'CNS', 'CNS')
  , ('irfo_auth_status', 'irfo_auth_s_granted', 'Granted', 'Granted')
  , ('irfo_auth_status', 'irfo_auth_s_pending', 'Pending', 'Pending')
  , ('irfo_auth_status', 'irfo_auth_s_renew', 'Renew', 'Renew')
  , ('irfo_auth_status', 'irfo_auth_s_withdrawn', 'Withdrawn', 'Withdrawn')
/* TL_REQUEST_TYPE */
  , ('insp_request_type', 'insp_req_t_new_op', 'New OP', '1')
  , ('insp_request_type', 'insp_req_t_var', 'Variation', '2')
  , ('insp_request_type', 'insp_req_t_fol', 'FOL', '3')
  , ('insp_request_type', 'insp_req_t_coe', 'Change of Entity', '4')
  , ('insp_request_type', 'insp_req_t_tc', 'TC Request', '5')
  , ('insp_request_type', 'insp_req_t_review', 'Review', '6')
  , ('insp_request_type', 'insp_req_t_comp', 'Compliance', '7')
/* TL_RESULT_TYPE */
  , ('insp_result_type', 'insp_res_t_new', 'New', '1')
  , ('insp_result_type', 'insp_res_t_new_sat', 'Satisfactory', '2')
  , ('insp_result_type', 'insp_res_t_new_unsat', 'Unsatisfactory', '3')
/* Interim Status */
  , ('interim_status', 'int_sts_granted', 'Granted', 'Granted')
  , ('interim_status', 'int_sts_in_force', 'In-Force', 'In-Force')
  , ('interim_status', 'int_sts_refused', 'Refused', 'Refused')
  , ('interim_status', 'int_sts_revoked', 'Revoked', 'Revoked')
  , ('interim_status', 'int_sts_saved', 'Saved', 'Saved')
/* TL_LIC_CAT */
  , ('lic_cat', 'lcat_gv', 'Goods Vehicle', 'GV')
  , ('lic_cat', 'lcat_permit', 'Permit', 'Permit')
  , ('lic_cat', 'lcat_psv', 'Public Service Vehicle', 'PSV')
/* TL_LIC_STATUS */
  , ('lic_status', 'lsts_cns', 'Continuation Not Sought', 'CNS')
  , ('lic_status', 'lsts_curtailed', 'Curtailed', 'Curtailed')
  , ('lic_status', 'lsts_granted', 'Granted', 'Granted')
  , ('lic_status', 'lsts_new', 'New', 'New')
  , ('lic_status', 'lsts_ntu', 'Not Taken Up', 'NTU')
  , ('lic_status', 'lsts_refused', 'Refused', 'Refused')
  , ('lic_status', 'lsts_revoked', 'Revoked', 'Revoked')
  , ('lic_status', 'lsts_surrendered', 'Surrendered', 'Surrendered')
  , ('lic_status', 'lsts_suspended', 'Suspended', 'Suspended')
  , ('lic_status', 'lsts_terminated', 'Terminated', 'Terminated')
  , ('lic_status', 'lsts_unlicenced', 'Unlicenced', 'Unlicenced')
  , ('lic_status', 'lsts_valid', 'Valid', 'Valid')
  , ('lic_status', 'lsts_withdrawn', 'Withdrawn', 'Withdrawn')
/* TL_LIC_TYPE */
  , ('lic_type', 'ltyp_cbp', 'Community', 'CBP')
  , ('lic_type', 'ltyp_dbp', 'Designated Body/Local Authority', 'DBP')
  , ('lic_type', 'ltyp_lbp', 'Large', 'LBP')
  , ('lic_type', 'ltyp_r', 'Restricted', 'R')
  , ('lic_type', 'ltyp_sbp', 'Small', 'SBP')
  , ('lic_type', 'ltyp_si', 'Standard International', 'SI')
  , ('lic_type', 'ltyp_sn', 'Standard National', 'SN')
  , ('lic_type', 'ltyp_sr', 'Special Restricted', 'SR')
  ;

INSERT INTO ref_data(
  ref_data_category_id, id, description, olbs_key
)values
/* TL_obj_grounds */
  ('obj_grounds', 'ogf_both', 'Both Obj and Env (Legacy ''B'')', 'B')
  , ('obj_grounds', 'ogf_env', 'Environmental (Legacy ''E'')', 'E')
  , ('obj_grounds', 'ogf_fin_stan', 'Financial Standing', 'Fin Stan')
  , ('obj_grounds', 'ogf_fitness', 'Fitness', 'Fitness')
  , ('obj_grounds', 'ogf_o', 'Objection (Legacy ''O'')', 'O')
  , ('obj_grounds', 'ogf_o_ccap', 'O/C Capacity', 'O/CCap')
  , ('obj_grounds', 'ogf_parking', 'Parking', 'Parking')
  , ('obj_grounds', 'ogf_prof_com', 'Professional Competence', 'Prof Com')
  , ('obj_grounds', 'ogf_repute', 'Repute', 'Repute')
  , ('obj_grounds', 'ogf_safety', 'Safety', 'Safety')
  , ('obj_grounds', 'ogf_size', 'Size', 'Size')
  , ('obj_grounds', 'ogf_unsochrs', 'Unsocial Hours', 'UnSocHrs')
  , ('obj_grounds', 'ogf_fumes', 'Fumes', 'Fumes')
  , ('obj_grounds', 'ogf_noise', 'Noise', 'Noise')
  , ('obj_grounds', 'ogf_pollution', 'Pollution', 'Pollut')
  , ('obj_grounds', 'ogf_vib', 'Vibration', 'Vibrat')
  , ('obj_grounds', 'ogf_vis', 'Visual Intrusion', 'Vis Int')
/* TL_OBJ_STATUS */
/* not used
  , ('obj_status', 'obj_sts_closed', 'Closed', null)
  , ('obj_status', 'obj_sts_negotiate', 'Negotiate', null)
  , ('obj_status', 'obj_sts_open', 'Open', null)
  , ('obj_status', 'obj_sts_pend', 'Pending', null)
  , ('obj_status', 'obj_sts_pub_inq', 'Public Inquiry', null)
  , ('obj_status', 'obj_sts_re_open', 'Re-open', null)
  */
/* TL_OBJECTOR_TYPE */
  , ('opposer_type', 'obj_t_local_auth', 'Local Authority', '1')
  , ('opposer_type', 'obj_t_police', 'Police', '2')
  , ('opposer_type', 'obj_t_rta', 'RTA', '3')
  , ('opposer_type', 'obj_t_trade_union', 'Trade Union', '4')
  , ('opposer_type', 'obj_t_other', 'Other', '5')
/* TL_OP_TYPE */
  /* Not in olcs - setting them to PA, ('org_type', 'org_t_irfo', 'IRFO Operator', 'IRFO') */
  , ('org_type', 'org_t_p', 'Partnership', 'P')
  , ('org_type', 'org_t_pa', 'Other (e.g. public authority, charity, trust, university)', 'PA')
  /* Not in olcs , ('org_type', 'pb', 'Permit Body', 'TL_OP_TYPE', 'PB') */
  , ('org_type', 'org_t_rc', 'Registered Company', 'RC')
  , ('org_type', 'org_t_st', 'Sole Trader', 'ST')
  , ('org_type', 'org_t_llp', 'LLP', 'LLP')
  /* Phone contact type New to olcs - See TL_PHONE_NO_TYPE*/
  , ('phone_contact_type', 'phone_t_tel', 'Business', 'Business')
  , ('phone_contact_type', 'phone_t_fax', 'Fax', 'Fax')
  , ('phone_contact_type', 'phone_t_gtn', 'GTN Code', null)
  , ('phone_contact_type', 'phone_t_home', 'Home', 'Home')
  , ('phone_contact_type', 'phone_t_mobile', 'Mobile', 'Mobile')
  /* Person title New for olcs - Keys odd to not break existing code if FK is added to model*/
  , ('person_title', 'Dr', 'Dr', null)
  , ('person_title', 'Miss', 'Miss', null)
  , ('person_title', 'Mr', 'Mr', null)
  , ('person_title', 'Mrs', 'Mrs', null)
  , ('person_title', 'Ms', 'Ms', null)
  , ('person_title', 'Other', 'Other', null)
/* public enquiry status */
  , ('pi_status', 'pi_s_schedule', 'PI Scheduled', 'SchedPI')
  , ('pi_status', 'pi_s_reg', 'PI Registered', 'RegisterPI')
/* previous licence type - used in completing the previous licence history section of an application*/
  , ('prev_licence_type', 'prev_has_licence', 'Named person on licence is on a current licence', null)
  , ('prev_licence_type', 'prev_had_licence', 'Named person on licence has been on previous licence', null)
  , ('prev_licence_type', 'prev_been_refused', 'Named person on licence has been refused a licence previously', null)
  , ('prev_licence_type', 'prev_been_revoked', 'Named person on licence has had a previous licence revoked, curtailed or suspended', null)
  , ('prev_licence_type', 'prev_been_at_pi', 'Named person on licence has been to a PI', null)
  , ('prev_licence_type', 'prev_been_disqualified_tc', 'Named person on licence has been disqualified by a TC', null)
  , ('prev_licence_type', 'prev_has_purchased_assets', 'Named person or company has purchased a company with a licence in the last 12 months', null)
/* Stay Status */
  , ('stay_status', 'stay_s_granted', 'GRANTED', '1')
  , ('stay_status', 'stay_s_refused', 'REFUSED', '0')
/* Stay Type*/
  , ('stay_type', 'stay_t_tc', 'TC', null)
  , ('stay_type', 'stay_t_UT', 'Upper Tribunal', null)
/* TL_COMPLIANCE_SIDV */
  , ('prohibition_type', 'pro_t_si', 'Immediate (S)', 'SI')
  , ('prohibition_type', 'pro_t_sd', 'Delayed (S)', 'SD')
  , ('prohibition_type', 'pro_t_sv', 'Variation (S)', 'SV')
  , ('prohibition_type', 'pro_t_i', 'Immediate', 'I')
  , ('prohibition_type', 'pro_t_d', 'Delayed', 'D')
  , ('prohibition_type', 'pro_t_v', 'Variation', 'V')
  , ('prohibition_type', 'pro_t_ro', 'Refusals Only', 'RO')
  , ('prohibition_type', 'pro_t_vr', 'Variation & Refusals Only', 'VR')
/* publication status */
  , ('publication_status', 'pub_s_generated', 'Generated', 'Generated')
  , ('publication_status', 'pub_s_new', 'New', 'New')
  , ('publication_status', 'pub_s_printed', 'Printed', 'Printed')
/* Note Type New to OLCS */
  , ('note_type', 'note_t_app', 'Application', null)
  , ('note_type', 'note_t_bus', 'Bus Registration', null)
  , ('note_type', 'note_t_case', 'Case', null)
  , ('note_type', 'note_t_person', 'Person', null)
  , ('note_type', 'note_t_lic', 'Licence', null)
  , ('note_type', 'note_t_irfo_gv', 'IRFO GV Permit', null)
  , ('note_type', 'note_t_irfo_psv', 'IRFO PSV Auth', null)
/* submission decision */
  , ('submission_decision', 'sub_d_agree', 'Agree', null)
  , ('submission_decision', 'sub_d_partial', 'Partially agree', null)
  , ('submission_decision', 'sub_d_disagree', 'Disagree', null)
  , ('submission_decision', 'sub_d_more_info', 'Further information required', null)
/* submission recommendation */
  , ('submission_recommendation', 'sub_r_other', 'Other', null)
  , ('submission_recommendation', 'sub_r_ptr', 'In-Office revokation', null)
  , ('submission_recommendation', 'sub_r_warn', 'Warning letter', null)
  , ('submission_recommendation', 'sub_r_nfa', 'NFA', null)
  , ('submission_recommendation', 'sub_r_cond', 'Undertakings & conditions', null)
  , ('submission_recommendation', 'sub_r_pi', 'Public Inquiry', null)
  , ('submission_recommendation', 'sub_r_prelim', 'Preliminary Hearing', null)
  , ('submission_recommendation', 'sub_r_stl', 'STL Interview', null)
/* Tachograh Inspection */
  , ('tach_ins', 'tach_internal', 'Internal', null)
  , ('tach_ins', 'tach_external', 'External', null)
  , ('tach_ins', 'tach_na', 'Not Applicable', null)
/* TL_PI_PRESIDING_TC_ROLE */
  , ('tc_role', 'tc_r_dhtru', 'Deputy Head of Traffic Regulation Unit', 'DHTRU')
  , ('tc_role', 'tc_r_dtc', 'Deputy Traffic Commissioner', 'DTC')
  , ('tc_role', 'tc_r_htru', 'Head of Traffic Regulation Unit', 'HTRU')
  , ('tc_role', 'tc_r_tc', 'Traffic Commissioner', 'TC')
/* TL_TM_COMPLIANCE_CASE_DECISION */
  , ('tm_case_decision', 'tm_decision_noa', 'No Further Action', 'NOA')
  , ('tm_case_decision', 'tm_decision_rl', 'Declare Unfit', 'RL')
  , ('tm_case_decision', 'tm_decision_rnl', 'Repute Not Lost', 'rnl')
/* TL_TM_COMPLIANCE_EPISODE_DECISION_REHAB_MEASURE */
  , ('tm_case_rehab', 'tm_rehab_adc', 'Additional Conditions On Licence', 'ADC')
  , ('tm_case_rehab', 'tm_rehab_adt', 'Additional Training', 'ADT')
  , ('tm_case_rehab', 'tm_rehab_oth', 'Other', 'OTH')
  , ('tm_case_rehab', 'tm_rehab_rlc', 'Relicensing', 'RLC')
  , ('tm_case_rehab', 'tm_rehab_rpt', 'Repeat Training', 'RPT')
/* TL_TM_COMPLIANCE_EPISODE_DECISION_UNFITNESS_REASON */
  , ('tm_unfit_reason', 'tm_unfit_inc', 'Infringement Of Community Rules', 'INC')
  , ('tm_unfit_reason', 'tm_unfit_inn', 'Infringement Of National Rules', 'INN')
/* TL_TM_COMPLIANCE_EPISODE_PI_REASON */
  , ('tm_pi_reason', 'tm_pi_reason_art6', 'Article 6 of Regulation (EC) No 1071/2009', 'ART6')
/* TL_TM_COMPLIANCE_EPISODE_PI_TYPE */
  , ('tm_pi_type', 'tm_pi_t_reg', 'Regulatory', 'REG')
/* TL_TM_QUAL_TYPE */
  , ('tm_qual_type', 'tm_qt_AR', 'AR', 'AR')
  , ('tm_qual_type', 'tm_qt_CPCSI', 'CPCSI', 'CPCSI')
  , ('tm_qual_type', 'tm_qt_CPCSN', 'CPCSN', 'CPCSN')
  , ('tm_qual_type', 'tm_qt_EXSI', 'EXSI', 'EXSI')
  , ('tm_qual_type', 'tm_qt_EXSN', 'EXSN', 'EXSN')
  , ('tm_qual_type', 'tm_qt_NIAR', 'NIAR', 'NIAR')
  , ('tm_qual_type', 'tm_qt_NICPCSI', 'NICPCSI', 'NICPCSI')
  , ('tm_qual_type', 'tm_qt_NICPCSN', 'NICPCSN', 'NICPCSN')
  , ('tm_qual_type', 'tm_qt_NIEXSI', 'NIEXSI', 'NIEXSI')
  , ('tm_qual_type', 'tm_qt_NIEXSN', 'NIEXSN', 'NIEXSN')
/* TL_TM_STATUS */
  , ('tm_status', 'tm_s_cur', 'Current', 'C')
  , ('tm_status', 'tm_s_dis', 'Disqualified', 'D')
/* TL_TM_TYPE */
  , ('tm_type', 'tm_t_B', 'Both', 'B')
  , ('tm_type', 'tm_t_E', 'External', 'E')
  , ('tm_type', 'tm_t_I', 'Internal', 'I')
/* TL_VAR_REASON - put in table for TXC stuff
  , ('var_reason', 'vr_timetable', 'Timetable', '1')
  , ('var_reason', 'vr_route', 'Timetable', '2')
  , ('var_reason', 'vr_s_f_point', 'Timetable', '3')
  , ('var_reason', 'vr_stop_places', 'Timetable', '4')

  */
/* TL_VHL_BODY_TYPE - Removed from  */
/* TL_VHL_REMOVAL_REASON */
  , ('vhl_removal_reason', 'vmr_cns', 'CNS', '1')
  , ('vhl_removal_reason', 'vmr_revoke', 'Revoke', '2')
  , ('vhl_removal_reason', 'vmr_surrender', 'Surrender', '3')
  , ('vhl_removal_reason', 'vmr_ntu', 'NTU', '4')
  , ('vhl_removal_reason', 'vmr_dup', 'Duplicate', '5')

/* TL_VHL_TYPE TODO these might need a change */
 , ('vhl_type', 'vhl_t_a', 'Small', 'A')
 , ('vhl_type', 'vhl_t_b', 'Medium', 'B')
 , ('vhl_type', 'vhl_t_c', 'Large', 'C')
 /* TL_WithdrawnReason */
 , ('withdrawn_reason', 'withdrawn', 'Withdrawn', '1')
 , ('withdrawn_reason', 'reg_in_error', 'Registered In Error', '2')
;



INSERT INTO `category` (`id`,`description`,`is_doc_category`,`is_task_category`,`created_by`,`last_modified_by`,`created_on`,`last_modified_on`,`version`) VALUES
    (1,'Licensing',1,1,NULL,NULL,NULL,NULL,1),
    (2,'Compliance',1,1,NULL,NULL,NULL,NULL,1),
    (3,'Bus Registration',1,1,NULL,NULL,NULL,NULL,1),
    (4,'Permits',1,1,NULL,NULL,NULL,NULL,1),
    (5,'Transport Manager',1,1,NULL,NULL,NULL,NULL,1),
    (7,'Environmental',1,1,NULL,NULL,NULL,NULL,1),
    (8,'IRFO',1,1,NULL,NULL,NULL,NULL,1),
    (9,'Application',0,1,NULL,NULL,NULL,NULL,1),
    (10,'Submission',0,1,NULL,NULL,NULL,NULL,1);

INSERT INTO task_sub_category(id,description,name,category_id,is_freetext_description) VALUES
    (1, 'Address Change ', 'Address Change Assisted Digital', 9, 0),
    (2, 'Address Change ', 'Address Change Digital', 9, 0),
    (3, 'Bank Statement', 'Bank Statement Assisted Digital', 9, 0),
    (4, 'Bank Statement', 'Bank Statement Digital', 9, 0),
    (5, 'Change of Partner(s)', 'Change of Partner Assisted Digital', 9, 0),
    (6, 'Change of Partner(s)', 'Change of Partner Digital', 9, 0),
    (7, 'Change of Director(s)', 'Director Assisted Digital App', 9, 0),
    (8, 'Change of Director(s)', 'Director Digital App', 9, 0),
    (9, 'Application Fee Due', 'Fee Due', 9, 0),
    (10, 'Grant Fee Due', 'Fee Due', 9, 0),
    (11, 'Interim Fee Due', 'Fee Due', 9, 0),
    (12, 'Application Fee Due', 'Fee Due', 9, 0),
    (13, null, 'Financial Document Assisted Digital', 9, 1),
    (14, null, 'Financial Document Digital', 9, 1),
    (15, 'GV79 Application', 'GV79 Assisted Digital', 9, 0),
    (16, 'GV79 Application', 'GV79 Digital', 9, 0),
    (17, 'GV80A', 'GV80A Assisted Digital', 9, 0),
    (18, 'GV80A', 'GV80A Digital', 9, 0),
    (19, 'GV81 Assisted Digital', 'GV81 Assisted Digital', 9, 0),
    (20, 'GV81 Digital', 'GV81 Digital', 9, 0),
    (21, 'Interim licence expiring', 'Interim', 9, 0),
    (22, 'Interim Request', 'Interim App Assisted Digital', 9, 0),
    (23, 'Interim Request', 'Interim App Digital', 9, 0),
    (24, 'Maintenance Contract', 'Maint Contract Assisted Digital', 9, 0),
    (25, 'Maintenance Contract', 'Maint Contract Digital', 9, 0),
    (26, 'PSV421 Application', 'PSV421 Assisted Digital', 9, 0),
    (27, 'PSV421 Application', 'PSV421 Digital', 9, 0),
    (28, 'PSV431 Application', 'PSV431 Assisted Digital', 9, 0),
    (29, 'PSV431 Application', 'PSV431 Digital', 9, 0),
    (30, null, 'Response to 1st Request ', 9, 1),
    (31, null, 'Response to Final Request', 9, 1),
    (32, 'Subsidiary Company change', 'Subsidiary Assisted Digital App', 9, 0),
    (33, 'Subsidiary Company change', 'Subsidiary Digital App', 9, 0),
    (34, 'New application ', 'Time expired', 9, 0),
    (35, 'Variation Application', 'Time expired', 9, 0),
    (36, 'New Application: {bus_reg_no}', 'EBSR', 3, 0),
    (37, 'Data Refresh: {bus_reg_no}', 'EBSR', 3, 0),
    (38, 'Variation: {bus_reg_no}', 'EBSR', 3, 0),
    (39, 'Cancellation: {bus_reg_no}', 'EBSR', 3, 0),
    (40, 'Bus Registration Fee Due', 'Fee Due', 3, 0),
    (41, 'Bus Registration Fee Due', 'Fee Due', 3, 0),
    (42, null, 'General Task', 3, 1),
    (43, 'SFTP Request {user_name}', 'SFTP request', 3, 0),
    (44, 'Bank Statement', 'Bank Statement', 2, 0),
    (45, 'ERRU Case:{case_id}', 'ERRU Auto Case', 2, 0),
    (46, null, 'TM Compliance Doc', 2, 1),
    (47, null, 'General Task', 2, 1),
    (48, 'GV79E', 'GV79E', 7, 0),
    (49, 'Objection', 'Objection', 7, 0),
    (50, 'Plan of OC', 'Plan of OC', 7, 0),
    (51, 'Representation', 'Representation', 7, 0),
    (52, 'Review Complaint', 'Review Complaint', 7, 0),
    (53, 'Review period complaint recorded', 'Review Period ends in 2 weeks', 7, 0),
    (54, null, 'General Task', 7, 1),
    (55, 'Application', 'Application', 8, 0),
    (56, 'Fee Due', 'Fee Due', 8, 0),
    (57, null, 'General Task', 8, 1),
    (58, 'Bank Statement', 'Bank Statement', 1, 0),
    (59, 'Old Licence: {lic_no}', 'Change of entity Assisted Digital', 1, 0),
    (60, 'Old Licence: {lic_no}', 'Change of entity Digital', 1, 0),
    (61, 'Checklist Not Received', 'Checklist Not received', 1, 0),
    (62, null, 'General Task', 1, 1),
    (63, 'Response To Request {inspection_request_id}', 'Inspection request/seminar', 1, 0),
    (64, 'Satisfactory Result {inspection_request_id}', 'Inspection request/seminar', 1, 0),
    (65, 'Unsatisfactory Result {inspection_request_id}', 'Inspection request/seminar', 1, 0),
    (66, 'ID:{organisation_id}/ Merge Lic: {lic_no}', 'Request to merge licences', 1, 0),
    (67, 'Surrender Request', 'Sur 1 Assisted Digital', 1, 0),
    (68, 'Surrender Request', 'Sur 1 Digital', 1, 0),
    (69, 'TM Required', 'TM Period of Grace', 1, 0),
    (70, 'Application', 'Application', 4, 0),
    (71, 'Fee Due', 'Fee Due', 4, 0),
    (72, null, 'General Task', 4, 1),
    (73, 'Case Decision', 'Decision', 10, 0),
    (74, 'Case Recommendation', 'Recommendation', 10, 0),
    (75, null, 'General Task', 5, 1),
    (76, 'Certificate', 'Certificate', 5, 0),
    (77, 'Close TM Case', 'Close TM Case', 5, 0),
    (78, 'TM Removed from this licence', 'TM Declared Unfit', 5, 0),
    (79, 'TM1 Application', 'TM1 Assisted Digital', 5, 0),
    (80, 'TM1 Application', 'TM1 Digital', 5, 0);

INSERT INTO `submission_section` (`id`, `description`, `group`) VALUES
    (1,'Offences (inc. driver hours)','Compliance'),
    (2,'Prohibitions','Compliance'),
    (3,'Convictions','Compliance'),
    (4,'Penalties','Compliance'),
    (5,'ERRU MSI','Compliance'),
    (6,'Bus compliance','Compliance'),
    (7,'Section 9','Compliance'),
    (8,'Section 43','Compliance'),
    (9,'Impounding','Compliance'),
    (10,'Duplicate TM','TM'),
    (11,'Repute / professional competence of TM','TM'),
    (12,'TM Hours','TM'),
    (13,'Interim with / without submission','Licensing application'),
    (14,'Representation','Licensing application'),
    (15,'Objection','Licensing application'),
    (16,'Non-chargeable variation','Licensing application'),
    (17,'Regulation 31/29','Licensing application'),
    (18,'Schedule 4/1','Licensing application'),
    (19,'Chargeable variation','Licensing application'),
    (20,'New application','Licensing application'),
    (21,'Surrender','Licence referral'),
    (22,'Non application related maintenance issue','Licence referral'),
    (23,'Review complaint','Licence referral'),
    (24,'Late fee','Licence referral'),
    (25,'Financial standing issue (continuation)','Licence referral'),
    (26,'Repute fitness of director','Licence referral'),
    (27,'Period of grace','Licence referral'),
    (28,'Proposal to revoke','Licence referral'),
    (29,'Yes','Bus registration');

INSERT INTO `conviction_category` (`id`, `created_by`, `last_modified_by`, `description`, `created_on`,
    `last_modified_on`, `version`, `parent_id`) VALUES
    (1,1,1,'70, 60 and 50 mph (Temporary Speed Limit) Order 1977','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (2,1,1,'Children & Young Persons (Scotland) Act 1937','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (3,1,1,'Civic Government (Scotland) Act 1982','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (4,1,1,'Criminal Law (Consolidation) (Scotland) Act 1995','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (5,1,1,'Criminal Procedure (Scotland) Act 1995','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (6,1,1,'Drivers\' Hours (Goods Vehicles)(Keeping of Records) Regulations 1987','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,NULL),
    (7,1,1,'Goods Vehicles (Licensing of Operators) Act 1995','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (8,1,1,'Goods Vehicles (Operators\' Licences, Qualifications and Fees) Regulations 1984','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,NULL),
    (9,1,1,'Hydrocarbon Oil Duties Act 1979','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (10,1,1,'Motor Vehicles (Driving Licences) (Heavy Goods and Public Service Vehicles) Regulations 1990',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (11,1,1,'Non Act','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (12,1,1,'Public Passenger Vehicles Act 1981','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (13,1,1,'Road Traffic (Driver Licensing and Information Systems) 1989','2002-01-22 09:53:03','2002-01-22 09:53:03',
    1,NULL),
    (14,1,1,'Road Traffic Act 1988','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (15,1,1,'Road Traffic Regulation Act 1984','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (16,1,1,'Road Transport (International Passenger Services) Regulations 1984','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,NULL),
    (17,1,1,'Road Vehicles (Construction and Use) Regulations 1986','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (18,1,1,'Road Vehicles (Registration and Licensing) Regulations 1971','2002-01-22 09:53:03','2002-01-22 09:53:03',1,
    NULL),
    (19,1,1,'Road Vehicles Lighting Regulations 1989','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (20,1,1,'Sex Offenders Act 1997','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (21,1,1,'Sexual Offences (Scotland) Act 1976','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (22,1,1,'Transit of Animals Order 1927','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (23,1,1,'Transport Act 1968','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (24,1,1,'Transport Act 2000','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (25,1,1,'Transport and Works Act 1992','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (26,1,1,'Vehicle Excise and Registration Act 1994','2002-01-22 09:53:03','2002-01-22 09:53:03',1,NULL),
    (27,1,1,'Exceeding 60mph on dual carriageway (offence detected involving the use of camera devices as provided for by Road Traffic Act 1991 s40)',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,1),
    (28,1,1,'Exceeding 50 mph on dual carriageway (offence detected involving the use of camera devices as provided for by Road Traffic Act 1991 s40)',
    '2002-01-22 09:53:03','2002- 01-22 09:53:03',1,1),
    (29,1,1,'Exceeding 50 mph on dual carriageway (other than detected involving the use of camera devices under Road Traffic Act 1991s40)',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,1),
    (30,1,1,'Exceeding 60mph on dual carriageway (other than detected involving the use of camera devices under Road Traffic Act 1991 s40)',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,1),
    (31,1,1,'Exceeding 50mph on single carriageway (offence detected involving the use of camera devices as provided for by Road Traffic Act 1991 s40)',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,1),
    (32,1,1,'Exceeding 50mph on single carriageway (other than detected involving the use of camera devices under Road Traffic Act 1991 s40)',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,1),
    (33,1,1,'Contravening temporary minimum speed limit imposed by Secretary of State (offence detected involving the use of camera devices as provided for by Road Traffic Act 1991 s40)',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,1),
    (34,1,1,'Exceeding 70mph on dual carriageway (offence detected involving the use of camera devices as provided for by Road Traffic Act 1991 s40)',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,1),
    (35,1,1,'Exceeding 60mph on single carriageway (offence detected involving the use of camera devices as provided for by Road Traffic Act 1991 s40)',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,1),
    (36,1,1,'Exceeding 70mph on dual carriageway (other than detected involving the use of camera devices under Road Traffic Act 1991 s40)',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,1),
    (37,1,1,'Procure - expose','2002-01-22 09:53:03','2002-01-22 09:53:03',1,2),
    (38,1,1,'Procure - wilfully expose','2002-01-22 09:53:03','2002-01-22 09:53:03',1,2),
    (39,1,1,'Wilfully assault','2002-01-22 09:53:03','2002-01-22 09:53:03',1,2),
    (40,1,1,'Wilfully expose','2002-01-22 09:53:03','2002-01-22 09:53:03',1,2),
    (41,1,1,'Distribute indecent photograph of child','2002-01-22 09:53:03','2002-01-22 09:53:03',1,3),
    (42,1,1,'Indecent photo of child - drafted style','2002-01-22 09:53:03','2002-01-22 09:53:03',1,3),
    (43,1,1,'Publish indecent photograph of a child','2002-01-22 09:53:03','2002-01-22 09:53:03',1,3),
    (44,1,1,'Take indecent photograph of a child','2002-01-22 09:53:03','2002-01-22 09:53:03',1,3),
    (45,1,1,'Possess indecent photograph of a child','2002-01-22 09:53:03','2002-01-22 09:53:03',1,3),
    (46,1,1,'Unlawful intercourse with girl under 13 years','2002-01-22 09:53:03','2002-01-22 09:53:03',1,4),
    (47,1,1,'Attempted unlawful intercourse with girl under 13 years','2002-01-22 09:53:03','2002-01-22 09:53:03',1,4),
    (48,1,1,'Attempted unlawful intercourse with girl between 13 & 16 years','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,4),
    (49,1,1,'Unlawful intercourse with girl between 13 & 16 years','2002-01-22 09:53:03','2002-01-22 09:53:03',1,4),
    (50,1,1,'Assault & attempted rape','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (51,1,1,'Assault & rape','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (52,1,1,'Assault to severe injury','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (53,1,1,'Assault to severe injury & rape','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (54,1,1,'Assault to severe injury with intent to rape','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (55,1,1,'Assault with intent to rape','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (56,1,1,'Assault with intent to rob & indecent assault','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (57,1,1,'Assault, abduction & rape','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (58,1,1,'Assault, attempted abduction & rape','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (59,1,1,'Assault, robbery & attempted rape','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (60,1,1,'Attempted rape','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (61,1,1,'Attempted rape & robbery','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (62,1,1,'Housebreaking with intent to rape','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (63,1,1,'Housebreaking, rape & robbery','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (64,1,1,'Permanent disfigurement & attempted rape','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (65,1,1,'Rape','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (66,1,1,'Rape & assault','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (67,1,1,'Rape & assault to severe injury','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (68,1,1,'Rape & robbery','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (69,1,1,'Rape, assault to severe injury & permanent disfigurement','2002-01-22 09:53:03','2002-01-22 09:53:03',1,5),
    (70,1,1,'Driver failing to return record book in which all weekly record sheets have been used to employer after 14 days from date on which it was last returned',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,6),
    (71,1,1,'Employer failing to examine completed weekly record sheet','2002-01-22 09:53:03','2002-01-22 09:53:03',1,
    6),
    (72,1,1,'Employer failing to return book to driver before he or she was next on duty','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,6),
    (73,1,1,'Employer failing to sign completed weekly or duplicate record sheet','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,6),
    (74,1,1,'Employer failing to enter specified details in driver\'s record book prior to issue','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,6),
    (75,1,1,'Owner-driver failing to preserve intact own duplicate driver\'s weekly record sheets for a year after book was completed or ceased to be used',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,6),
    (76,1,1,'Driver failing to return driver\'s record book to employer on ceasing employment','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,6),
    (77,1,1,'Employer failing to preserve intact driver\'s record book for a year after book was returned',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,6),
    (78,1,1,'Employer failing to preserve intact duplicate driver\'s weekly record sheets for a year after book was returned',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,6),
    (79,1,1,'Driver using second record book before completion of all record sheets in first record book',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,6),
    (80,1,1,'Owner-driver failing to preserve intact own driver\'s record book for a year after book was completed or ceased to be used',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,6),
    (81,1,1,'Driver failing to complete required manual record','2002-01-22 09:53:03','2002-01-22 09:53:03',1,6),
    (82,1,1,'Owner-driver failing to complete front sheet of driver\'s record book before use','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,6),
    (83,1,1,'Driver failing to make duplicate entry on weekly record sheet','2002-01-22 09:53:03','2002-01-22 09:53:03',
    1,6),
    (84,1,1,'Driver failing to deliver driver\'s record book to employer','2002-01-22 09:53:03','2002-01-22 09:53:03',1,
    6),
    (85,1,1,'Employer failing to detach completed duplicate record sheet','2002-01-22 09:53:03','2002-01-22 09:53:03',1,
    6),
    (86,1,1,'Driver failing to retain record book in which all weekly record sheets have been used for 14 days from date on which it was last returned',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,6),
    (87,1,1,'Using vehicle with no tachograph installed','2002-01-22 09:53:03','2002-01-22 09:53:03',1,6),
    (88,1,1,'Driver making entry in second driver\'s record book','2002-01-22 09:53:03','2002-01-22 09:53:03',1,6),
    (89,1,1,'Owner-driver failing to submit duplicate of weekly record sheet','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,6),
    (90,1,1,'Driver erasing or obliterating entry in driver\'s record book','2002-01-22 09:53:03','2002-01-22 09:53:03',
    1,6),
    (91,1,1,'Driver correcting entry in driver\'s record book in wrong fashion','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,6),
    (92,1,1,'Driver failing to sign correction in driver\'s record book','2002-01-22 09:53:03','2002-01-22 09:53:03',1,
    6),
    (93,1,1,'Driver\'s second employer failing to complete front sheet of driver\'s record book','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,6),
    (94,1,1,'Driver failing to produce driver\'s record book for inspection','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,6),
    (95,1,1,'Driver\'s second employer failing to require production of driver\'s record book','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,6),
    (96,1,1,'Driver failing to carry driver\'s record book','2002-01-22 09:53:03','2002-01-22 09:53:03',1,6),
    (97,1,1,'Goods Vehicle - Alter Operator\'s Disc','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (98,1,1,'Goods Vehicle - Operators disc illegible.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (99,1,1,'Parking HGV on central reservation on footway comprised on an urban road','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,7),
    (100,1,1,'Permitting use of vehicle without plating certificate','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (101,1,1,'Used a goods vehicle when the licence was restricted.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (102,1,1,'drove a large goods vehicle without a consignment note.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (103,1,1,'having used a large goods vehicle failed to preserve the consignment note.','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,7),
    (104,1,1,'contravention of a condition attached to a licence under Section 22 of the act in that.......',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (105,1,1,'the holder contravened a condition attached to the licence under section 23 of the act in that.......',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (106,1,1,'applied for or obtained an operators licence while disqualified.','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,7),
    (107,1,1,'Altering a document (goods vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (108,1,1,'Exceeded that number in contravention of Section 6(3).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (109,1,1,'knowingly made a false statement for the purpose of obtaining the variation of/ an operators licence/ a certificate of qualification under section 49 of the act/ a certificate or diploma such as is mentioned in para.13(1) of schedule 3 to the act.',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (110,1,1,'obstructing an officer exercising powers (mantenance facilities).','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,7),
    (111,1,1,'knowingly made a false statement for the purpose of obtaining the issue to yourself or another of/ an operators licence/ a certificate of qualification under section 49 of the act/ a certificate or diploma such as is mentioned in para.13(1) of schedule 3 ',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (112,1,1,'knowingly made a false statement for the purpose of preventing the issue or variation of / an operators licence/ a certificate of qualification under section 49 of the act/ a certificate or diploma such as is mentioned in para. 13(1) of schedule 3 to the Act',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (113,1,1,'Exceeded that number in contravention of Section 6(4)(a).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,
    7),
    (114,1,1,'Exceeded that number in contravention of Section 6(4)(b).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,
    7),
    (115,1,1,'holder of an operators licence exceeding maximum specified number of trailers','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,7),
    (116,1,1,'the holder of an operators licence exceeding the weight specified for the maximum number of trailers.',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (117,1,1,'used a place as an operating centre without that place being specified.','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,7),
    (118,1,1,'on application for an operators licence failed to notify the traffic commissioner that a notifiable conviction had occurred.',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (119,1,1,'Using vehicle with no goods vehicle test certificate','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (120,1,1,'Contravening a condition attached to a licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (121,1,1,'make a consignment note which you know to be false.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (122,1,1,'obstruct an officer exercising inspection powers.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (123,1,1,'Allowing document to be used by another (goods vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,
    7),
    (124,1,1,'Forging a document (goods vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (125,1,1,'Using a document (goods vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (126,1,1,'Lending document to another (goods vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (127,1,1,'with intent to deceive had in your possession a document or thing closely resembling/ an operators licence/ a document,plate,mark or other thing by which a vehicle is to be identified as being authorised to be used, or as being used,under an operators licence',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (128,1,1,'knowingly made a false statement for the purpose of procuring the imposition of a condition or limitation in relation to/ an operators licence/ a certificate of qualification under section 49 of the act/ a certificate or diploma such as is mentioned in paragraph 13(1) of Schedule 3',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (129,1,1,'With intent to deceive made / an operators licence / a document, plate or mark or other thing by which a vehicle is to be identified as being authorised to be used or as being used under an operators licence',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (130,1,1,'Goods Vehicle - Fail to display operators disc.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (131,1,1,'alter a consignment note','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (132,1,1,'cause a consignment note to be altered.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (133,1,1,'caused a consignment note to be made false.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (134,1,1,'Failure to return licence when required to do so','2002-01-22 09:53:03','2002-01-22 09:53:03',1,8),
    (135,1,1,'Failure to notify change of address','2002-01-22 09:53:03','2002-01-22 09:53:03',1,8),
    (136,1,1,'Failing to produce operator\'s licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,8),
    (137,1,1,'Failure to notify loss of identity disc or to return rediscovered disc when duplicate issued.',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,8),
    (138,1,1,'Acquiring duty-free oil in deliberate contravention of restriction in Act','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,9),
    (139,1,1,'Using duty-free oil in deliberate contravention of restriction in Act','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,9),
    (140,1,1,'Supplying duty-free oil with intent to contravene restriction in Act','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,9),
    (141,1,1,'Allowing duty-free oil to be taken into vehicle appliance or storage tank with intent to contravene restriction in Act',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,9),
    (142,1,1,'Using rebated heavy oil in deliberate contravention of restriction in Act ','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,9),
    (143,1,1,'Supplying rebated heavy oil with intent to contravene restriction in Act','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,9),
    (144,1,1,'Knowingly allowing rebated heavy oil to be taken into vehicle with intent to contravene restriction in Act',
    '2002-01-22 09:53:03','2002-01-22 09:53:03',1,9),
    (145,1,1,'Using light oil in deliberate contravention of restriction in Act','2002-01-22 09:53:03',
    '2002-01-22 09:53:03',1,9),
    (146,1,1,'Allowing light oil to be taken into vehicle appliance or storage tank with intent to contravene restriction in Act','2002-01-22 09:53:03','2002-01-22 09:53:03',1,9),
    (147,1,1,'Failing to notify traffic commissioner of loss of HGV licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (148,1,1,'Failing to notify traffic commissioner of loss of PSV licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (149,1,1,'Failing to produce HGV licence for examination by authorised officer','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (150,1,1,'Failing to produce PSV licence for examination by authorised officer','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (151,1,1,'Failing to return defaced HGV licence to traffic commissioner','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (152,1,1,'Failing to return defaced PSV licence to traffic commissioner','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (153,1,1,'Failing to return previously lost HGV licence to traffic commissioner','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (154,1,1,'Failing to return previously lost PSV licence to traffic commissioner','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (155,1,1,'Failing to sign duplicate HGV licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (156,1,1,'Failing to sign duplicate PSV licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (157,1,1,'Failing to surrender existing HGV licence upon refusal of licence which would have been under Road Traffic Act 1988 Part III','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (158,1,1,'Failing to surrender existing HGV licence upon revocation of licence which was issued under Road Traffic Act 1988 Part III','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (159,1,1,'Failing to surrender existing PSV licence upon refusal of licence which would have been under Road Traffic Act 1988 Part III','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (160,1,1,'Failing to surrender existing PSV licence upon revocation of licence which was issued under Road Traffic Act 1988 Part III','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (161,1,1,'Failing to surrender HGV licence on change of name and/or address','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (162,1,1,'Failing to surrender HGV licence on disqualification','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (163,1,1,'Failing to surrender PSV licence on change of name and/or address','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (164,1,1,'Failing to surrender PSV licence on disqualification','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (165,1,1,'Failing to surrender revoked HGV licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (166,1,1,'Failing to surrender revoked PSV licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (167,1,1,'Failing to surrender suspended PSV licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (168,1,1,'User Defined:','2002-01-22 09:53:03','2002-01-22 09:53:03',1,11),
    (169,1,1,'Failing to produce licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (170,1,1,'Contravening PSV and Trolley Vehicles (Carrying Capacity) Regulations 1954','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (171,1,1,'Contravention of or failure to comply with regulation of fitness for public passenger vehicle.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (172,1,1,'Failing to exhibit an operator\'s disc.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (173,1,1,'Using motor vehicle or trailer with defective tyre, insufficient tread.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (174,1,1,'Using a Public Service Vehicle without a certificate of initial fitness.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (175,1,1,'Making false statement to obtain certificate of approval of type vehicle.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (176,1,1,'Making false statement to obtain certificate of initial fitness.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (177,1,1,'Making false statement to obtain certificate of qualification.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (178,1,1,'Making false statement to obtain driver\'s licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (179,1,1,'Making false statement to obtain operator\'s disc.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (180,1,1,'Making false statement to obtain PSV operator\'s licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (181,1,1,'Making false statement to obtain road service licence.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (182,1,1,'Allowing use of road service licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (183,1,1,'Altering road service licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (184,1,1,'Forging road service licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (185,1,1,'Fraudulently using road service licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (186,1,1,'Lending road service licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (187,1,1,'Making false road service licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (188,1,1,'Altering PSV operator\'s licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (189,1,1,'Forging driver\'s licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (190,1,1,'Forging PSV operator\'s licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (191,1,1,'Fraudulently using PSV operator\'s licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (192,1,1,'Lending driver\'s licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (193,1,1,'Altering driver\'s licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (194,1,1,'Allowing use of driver\'s licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (195,1,1,'Allowing use of PSV operator\'s licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (196,1,1,'Fraudulently using driver\'s licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (197,1,1,'Lending PSV operator\'s licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (198,1,1,'Making false driver\'s licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (199,1,1,'Making false PSV operator\'s licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (200,1,1,'Possessing false driver\'s licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (201,1,1,'Possessing false PSV operator\'s licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (202,1,1,'Allowing use of certificate of initial fitness with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (203,1,1,'Altering certificate of initial fitness with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (204,1,1,'Forging certificate of initial fitness with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (205,1,1,'Fraudulently using certificate of initial fitness with intent to deceive ','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (206,1,1,'Lending certificate of initial fitness with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (207,1,1,'Making false certificate of initial fitness with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (208,1,1,'Possessing false certificate of initial fitness with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (209,1,1,'Allowing use of certificate of approval of type vehicle with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (210,1,1,'Altering certificate of approval of type vehicle with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (211,1,1,'Forging certificate of approval of type vehicle with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (212,1,1,'Fraudulently using certificate of approval of type vehicle with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (213,1,1,'Lending certificate of approval of type vehicle with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (214,1,1,'Making false certificate of approval of type vehicle with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (215,1,1,'Possessing false certificate of approval of type vehicle with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (216,1,1,'Altering operator\'s disc with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (217,1,1,'Forging operator\'s disc with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (218,1,1,'Lending operator\'s disc with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (219,1,1,'Allowing use of operator\'s disc with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (220,1,1,'Fraudulently using operator\'s disc with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (221,1,1,'Making false operator\'s disc with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (222,1,1,'Possessing false operator\'s disc with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (223,1,1,'Allowing use of certificate of qualification with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (224,1,1,'Fraudulently using certificate of qualification with intent to receive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (225,1,1,'Lending certificate of qualification with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (226,1,1,'Making false certificate of qualification with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (227,1,1,'Possessing false certificate of qualification with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (228,1,1,'Altering certificate of qualification with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (229,1,1,'Forging certificate of qualification with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (230,1,1,'Allowing use of with intent to deceive document evidencing appointment of person as certifying officer or PSV examiner','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (231,1,1,'Altering with intent to deceive document evidencing appointment of person as certifying officer or PSV examiner','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (232,1,1,'Forging with intent to deceive document evidencing appointment of person as certifying officer or PSV examiner','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (233,1,1,'Fraudulently using with intent to deceive document evidencing appointment of person as certifying officer or PSV examiner','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (234,1,1,'Lending with intent to deceive document evidencing appointment of person as certifying officer or PSV examiner','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (235,1,1,'Making with intent to deceive false document evidencing appointment of person as certifying officer or PSV examiner','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (236,1,1,'Possessing with intent to deceive false document evidencing appointment of person as certifying officer or PSV examiner','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (237,1,1,'Possessing false road service licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,12),
    (238,1,1,'Failing to surrender HGV licence after existing licence issued under Road Traffic Act 1988 Part III was revoked or surrendered','2002-01-22 09:53:03','2002-01-22 09:53:03',1,13),
    (239,1,1,'Failing to surrender PSV licence after existing licence issued under Road Traffic Act 1988 Part III was revoked or surrendered','2002-01-22 09:53:03','2002-01-22 09:53:03',1,13),
    (240,1,1,'Driver of passenger-carrying vehicle failing to produce PCV driving licence to authorised officer','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (241,1,1,'failed to produce a consignment note.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,7),
    (242,1,1,'Failing to surrender revoked PCV licence when required','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (243,1,1,'Failing to surrender suspended PCV licence when required','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (244,1,1,'Driver of goods vehicle failing to produce LGV driving licence and its counterpart to authorised officer','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (245,1,1,'Driver of goods vehicle failing to produce LGV driving licence to authorised officer','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (246,1,1,'Driver of passenger-carrying vehicle failing to produce PCV driving licence and its counterpart to authorised officer','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (247,1,1,'Using goods vehicle without operator\'s licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (248,1,1,'fradulently using a vehicle licence, trade licence, registration mark or registration document.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (249,1,1,'The use of a goods vehicle without an operators licence.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (250,1,1,'Failing to produce plating certificate issued in respect of goods vehicle and/or of trailer drawn by it when required to do so by constable','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (251,1,1,'Failing to produce test certificate issued in respect of goods vehicle and/or of trailer drawn by it when required to do so by constable','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (252,1,1,'Failing to give name and address and produce insurance and goods vehicle test certificate to constable after accident/suspected offence on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (253,1,1,'Failing to give name and address and produce insurance and plating certificate to constable after accident/suspected offence on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (254,1,1,'Failing to give name and address and produce insurance to constable after accident in a public place other than on a road by driver of goods\' vehicle.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (255,1,1,'Failing to give name and address and produce insurance, plating certificate and goods vehicle test certificate to constable after accident/suspected offence on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (256,1,1,'Failing to give name and address of vehicle owner and produce insurance and goods vehicle test certificate to constable after accident/suspected offence on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (257,1,1,'Failing to give name and address of vehicle owner and produce insurance and plating certificate to constable after accident/suspected offence on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (258,1,1,'Failing to give name and address of vehicle owner and produce insurance to constable after accident in a public place other than on a road by driver of goods\' vehicle.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (259,1,1,'Failing to give name and address of vehicle owner and produce insurance to constable after accident/suspected offence on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (260,1,1,'Failing to give name and address of vehicle owner and produce insurance, plating certificate and goods vehicle test certificate to constable after accident/suspected offence on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (261,1,1,'Failing to give name and address, name and address of vehicle owner and produce insurance and goods vehicle test certificate to constable after accident/suspected offence on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (262,1,1,'Failing to give name and address, name and address of vehicle owner and produce insurance and plating certificate to constable after accident/suspected offence on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (263,1,1,'Failing to give name and address, name and address of vehicle owner and produce insurance to constable after accident in a public place other than on a road by driver of goods\' vehicle.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (264,1,1,'Failing to give name and address, name and address of vehicle owner and produce insurance to constable after accident/suspected offence on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (265,1,1,'Failing to give name and address, name and address of vehicle owner and produce insurance, plating certificate and goods vehicle test certificate to constable after accident/suspected offence on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (266,1,1,'Failing to produce insurance and goods vehicle test certificate to constable after accident/suspected offence by driver of goods vehicle on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (267,1,1,'Failing to produce insurance and plating certificate to constable after accident/suspected offence by driver of goods vehicle on a road','2002-01-22 09:53:03','2002- 01-22 09:53:03',1,14),
    (268,1,1,'Failing to produce insurance to constable after accident/suspected offence by driver of goods vehicle in a public place other than on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (269,1,1,'Failing to produce insurance to constable after accident/suspected offence by driver of goods vehicle on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (270,1,1,'Failing to produce insurance, plating certificate and goods vehicle testing certificate to constable after accident/suspected offence by driver of goods vehicle on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (271,1,1,'Failing to produce plating and test certificate issued in respect of goods vehicle and/or of trailer drawn by it when required to do so by constable','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (272,1,1,'Keeper of vehicle failing to supply information as to driver\'s identity as required by or on behalf of Chief Officer of Police ','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (273,1,1,'Person other than keeper of vehicle failing to supply relevant information as required by or on behalf of Chief Officer of Police','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (274,1,1,'Making false statement to obtain operator\'s licence.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (275,1,1,'Knowingly making a false statement for the purpose of obtaining the grant of an international road haulage permit to himself or any other person.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (276,1,1,'Knowingly producing false evidence for the purposes of regulations under section 66(1) of this Act. (Applying for a licence under the Vehicles (Excise) Act 1971.)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (277,1,1,'Operating PSV without operator\'s licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (278,1,1,'Parking HGV on verge of road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (279,1,1,'Parking HGV on central reservation in land situated between two carriageways and which is not footway','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (280,1,1,'Exceeding 30mph on restricted road (other than detected involving the use of camera devices under Road Traffic Act 1991 s40)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (281,1,1,'Causing use of vehicle without plating certificate','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (282,1,1,'used a large goods vehicle withiot a consignment note.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (283,1,1,'Using vehicle without plating certificate','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (284,1,1,'Causing vehicle to which s53(1) of this Act applies to be used to draw trailer without plating certificate specifying maximum laden weight for vehicle plus trailer','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (285,1,1,'Permitting vehicle to which s 53(1) of this Act applies to be used to draw trailer without plating certificate specifying maximum laden weight for vehicle plus trailer','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (286,1,1,'Using vehicle to which s53(1) of this Act applies to draw trailer without plating certificate specifying maximum laden weight for vehicle plus trailer','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (287,1,1,'Obstructing an authorised examiner acting under this section or failing to comply with requirement in s67 or Sch 2 of this Act concerning testing vehicle on road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (288,1,1,'Obstructing authorised examiner from testing vehicle on road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (289,1,1,'Permitting another to drive a vehicle in contravention of a prohibition under ss69 or 70 of this Act','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (290,1,1,'Failing to surrender suspended HGV licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,10),
    (291,1,1,'Obstructing an authorised examiner acting under this section.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (292,1,1,'Owner of vehicle failing to give information as to insurance to police','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (293,1,1,'Making a false statement to obtain insurance.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (294,1,1,'Withholding information to obtain insurance.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (295,1,1,'Aiding and abetting use of vehicle with no test certificate','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (296,1,1,'Causing use of vehicle with no test certificate','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (297,1,1,'Permitting use of vehicle with no test certificate','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (298,1,1,'Using vehicle with no test certificate','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (299,1,1,'Causing use of vehicle with no goods vehicle test certificate','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (300,1,1,'issued the particulars of a transport manager in a standard licence application and failing to notify the traffic commissioner that the transport manager had been convicted of ......','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (301,1,1,'Permitting use of vehicle with no goods vehicle test certificate','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (302,1,1,'Causing use of vehicle which does not comply with or to which is fitted part which does not comply with prescribed requirement','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (303,1,1,'Permitting use of vehicle which does not comply with or to which is fitted part which does not comply with prescribed requirement','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (304,1,1,'Using vehicle which does not comply with or to which is fitted part which does not comply with prescribed requirement','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (305,1,1,'Selling defective or unsuitable vehicle part','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (306,1,1,'Aiding and abetting use of vehicle without insurance','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (307,1,1,'Using vehicle without insurance','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (308,1,1,'Altering document with intent to deceive (not otherwise coded)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (309,1,1,'Altering insurance document with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (310,1,1,'Altering International Road Haulage Permit with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (311,1,1,'Forging document with intent to deceive (not otherwise coded)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (312,1,1,'Forging insurance document with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (313,1,1,'Forging International Road Haulage Permit with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (314,1,1,'Forging licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (315,1,1,'Possessing insurance document with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (316,1,1,'Using document with intent to deceive (not otherwise coded)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (317,1,1,'Using insurance document with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (318,1,1,'Using International Road Haulage Permit with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (319,1,1,'Using licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (320,1,1,'Allowing document to be used with intent to deceive (not otherwise coded)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (321,1,1,'Allowing insurance document to be used with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (322,1,1,'Allowing licence to be used with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (323,1,1,'Driver failing to wear seat belt.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (324,1,1,'Lending document with intent to deceive (not otherwise coded)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (325,1,1,'Lending insurance document with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (326,1,1,'Lending International Road Haulage Permit with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (327,1,1,'Lending licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (328,1,1,'Allowing International Road Haulage Permit to be used with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (329,1,1,'Making document with intent to deceive (not otherwise coded)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (330,1,1,'Making insurance document with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (331,1,1,'Making International Road Haulage Permit with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (332,1,1,'Making licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (333,1,1,'Possessing document with intent to deceive (not otherwise coded)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (334,1,1,'Possessing International Road Haulage Permit with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (335,1,1,'Possessing licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (336,1,1,'Driving motor vehicle whilst unfit to drive through drink or drug (drink)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (337,1,1,'Aid/abet danger of injury due to vehicle condition.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (338,1,1,'Aid/abet danger of injury due to vehicle condition. (Condition of accessories or equipment).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (339,1,1,'Aid/abet danger of injury due to vehicle condition. (Load/passengers).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (340,1,1,'Aided, abetted, danger of injury due to vehicle condition. vehicle being used other than for its original purpose.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (341,1,1,'Causing danger of injury due to vehicle condition (accessories or equipment).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (342,1,1,'Causing danger of injury due to vehicle condition (load/passengers).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (343,1,1,'Danger of injury due to vehicle condition (accessories or equipment).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (344,1,1,'Danger of injury due to vehicle condition (load/passengers).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (345,1,1,'Danger of injury due to vehicle condition.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (346,1,1,'Permitting danger of injury due to vehicle condition (accessories or equipment).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (347,1,1,'Permitting danger of injury due to vehicle condition (load/passengers).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (348,1,1,'Permitting danger of injury due to vehicle condition.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (349,1,1,'Using vehicle with insecure load which is likely to cause danger','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (350,1,1,'Causing another to drive a vehicle in contravention of a prohibition under ss69 or 70 of this Act','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (351,1,1,'Failing to comply within a reasonable time with a direction under Section 70(3).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (352,1,1,'Driver having working day in excess of 11 hours','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (353,1,1,'Altering vehicle or trailer so as to be unroadworthy','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (354,1,1,'Causing alteration of vehicle or trailer so as to be unroadworthy','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (355,1,1,'Permitting alteration of vehicle or trailer so as to be unroadworthy','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (356,1,1,'Fitting defective or unsuitable vehicle part','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (357,1,1,'Causing the fitting of defective or unsuitable vehicle part','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (358,1,1,'Permitting the fitting of defective or unsuitable vehicle part','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (359,1,1,'Refusing or neglecting to comply with any requirement under subsection (1) (allowing a vehicle or trailer to be weighed) or (2) (enabling a vehicle or a trailer to be weighed).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (360,1,1,'Keeper of vehicle failing to give information concerning identity of driver','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (361,1,1,'Person other than keeper of vehicle failing to give information concerning identity of driver','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (362,1,1,'Contravening temporary road traffic prohibition or restriction made by Highway Authority on motorway under s14 of this Act (motor vehicle) (other than exceeding temporary speed restriction)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (363,1,1,'Exceeding temporary speed restriction made by Highway Authority on road other than motorway under s14 of this Act (motor vehicle)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (364,1,1,'Failing to surrender revoked LGV licence when required','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (365,1,1,'Causing use of a vehicle contravening oneway traffic on a trunk road.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (366,1,1,'Permitting use of a vehicle contravening oneway traffic on a trunk road.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (367,1,1,'Using a vehicle contravening oneway traffic on a trunk road.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (368,1,1,'Exceeding weight limit on specified road ','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (369,1,1,'Causing use of prohibited vehicle on a restricted road.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (370,1,1,'Permitting use of prohibited vehicle on a restricted road.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (371,1,1,'Using prohibited vehicle on a restricted road.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (372,1,1,'Causing vehicle use in designated play street.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (373,1,1,'Permitting vehicle use in designated play street.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (374,1,1,'Using vehicle in a designated play street','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (375,1,1,'Causing loading where loading/unloading is prohibited.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (376,1,1,'Causing stopping on a clearway.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (377,1,1,'Causing waiting where prohibited in peak time.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (378,1,1,'Causing waiting where waiting prohibited.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (379,1,1,'Loading where loading/unloading is prohibited.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (380,1,1,'Permitting loading where loading/unloading is prohibited.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (381,1,1,'Permitting stopping on a clearway.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (382,1,1,'Permitting waiting where prohibited in peak time.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (383,1,1,'Permitting waiting where waiting prohibited.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (384,1,1,'Stopping on a clearway.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (385,1,1,'Using a vehicle in contravention of a traffic regulation order outside Greater London.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (386,1,1,'Waiting where prohibited in peak time.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (387,1,1,'Waiting where waiting prohibited','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (388,1,1,'Exceeding 30mph on restricted road (offence detected involving the use of camera devices as provided for by Road Traffic Act 1991 s40)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (389,1,1,'Exceeding speed limit created by local order (offence not detected by camera devices)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (390,1,1,'Exceeding the speed indicated by traffic signs on unrestricted roads (involving the use of camera devices).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (391,1,1,'Exceeding the speed indicated by traffic signs on unrestricted roads.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (392,1,1,'Exceeding speed limit on unrestricted road (offence detected involving the use of camera devices as provided for by Road Traffic Act 1991 s40)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (393,1,1,'Exceeding speed limit on unrestricted road (other than detected involving the use of camera devices under Road Traffic Act 1991 s40)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (394,1,1,'Exceeding goods vehicle speed limit (offence detected involving the use of camera devices as provided for by Road Traffic Act 1991 s40)','2002-01-22 09:53:03','2002- 01-22 09:53:03',1,15),
    (395,1,1,'Exceeding goods vehicle speed limit (other than detected involving the use of camera devices under Road Traffic Act 1991 s40)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (396,1,1,'Exceeding passenger vehicle speed limit (offence detected involving the use of camera devices as provided for by Road Traffic Act 1991 s40)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (397,1,1,'Exceeding passenger vehicle speed limit (other than detected involving the use of camera devices under Road Traffic Act 1991 s40)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (398,1,1,'Speeding with trailer (offence detected involving the use of camera devices as provided for by Road Traffic Act 1991 s40)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (399,1,1,'Using vehicle not fitted with obligatory lighting equipment','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (400,1,1,'Failing to comply with minimum speed limit.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (401,1,1,'Contravening temporary minimum speed limit imposed by Secretary of State (other than detected involving the use of camera devices under Road Traffic Act 1991 s40)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (402,1,1,'Using low platform trailer in excess of 40 mph.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (403,1,1,'Exceeding 50mph on a duel carriageway (offence detected involving the use of camera devices as provided for by Road Traffic Act 1991 s40)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (404,1,1,'Exceeding 60mph on single carriageway (other than detected involving the use of camera devices under Road Traffic Act 1991 s40)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (405,1,1,'Exceeding speed limit created by local order (offence detected by camera devices)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (406,1,1,'Exceeding 70 mph on motorway (other than detected involving the use of camera devices under Road Traffic Act 1991 s40)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (407,1,1,'Exceeding temporary speed restriction made by Highway Authority on motorway under s14 of this Act (motor vehicle)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (408,1,1,'Causing vehicle to be used for Community-regulated carriage of passengers by road which contravenes relevant provision concerning type of service provided','2002-01-22 09:53:03','2002-01-22 09:53:03',1,16),
    (409,1,1,'Permitting vehicle to be used for Community-regulated carriage of passengers by road which contravenes relevant provision concerning type of service provided','2002- 01-22 09:53:03','2002-01-22 09:53:03',1,16),
    (410,1,1,'Using vehicle for Community-regulated carriage of passengers by road which contravenes relevant provision concerning type of service provided','2002-01-22 09:53:03','2002-01-22 09:53:03',1,16),
    (411,1,1,'Using vehicle for ASOR- or Community-regulated carriage of passengers by road which contravenes relevant provision concerning control documents','2002-01-22 09:53:03','2002-01-22 09:53:03',1,16),
    (412,1,1,'Contravening or failing to comply with requirement imposed by relevant regulation concerning control documents','2002-01-22 09:53:03','2002-01-22 09:53:03',1,16),
    (413,1,1,'Wilfully obstructing examiner in exercise of power under relevant regulation concerning control documents','2002-01-22 09:53:03','2002-01-22 09:53:03',1,16),
    (414,1,1,'Causing PSV to be used for international carriage of passengers without authorisation (regular and shuttle services non-Community-regulated)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,16),
    (415,1,1,'Failing to comply with requirement of certifying officer or PSV examiner relating to use of PSV for international carriage of passengers (regular and shuttle services non-Community- regulated)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,16),
    (416,1,1,'Permitting PSV to be used for international carriage of passengers without authorisation (regular and shuttle services non-Community-regulated)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,16),
    (417,1,1,'Wilfully obstructing certifying officer or PSV examiner in relation to use of PSV for international carriage of passengers (regular and shuttle services non- Community- regulated)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,16),
    (418,1,1,'Causing vehicle to be used for international carriage of passengers without complying with relevant Article concerning control documents (occasional services)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,16),
    (419,1,1,'Failing to comply with requirement of certifying officer or PSV examiner relating to use of vehicle for international carriage of passengers (occasional services)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,16),
    (420,1,1,'Permitting vehicle to be used for international carriage of passengers without complying with relevant Article concerning control documents (occasional services)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,16),
    (421,1,1,'Wilfully obstructing certifying officer or PSV examiner in relation to use of vehicle for international carriage of passengers (occasional services)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,16),
    (422,1,1,'Causing the use of a vehicle with no speed limiter plate fitted.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (423,1,1,'Obstructing examiner testing vehicle to ascertain if defective or unsuitable part has been fixed etc','2002-01-22 09:53:03','2002-01-22 09:53:03',1,14),
    (424,1,1,'Causing another to use a vehicle over 3.66m in height without a notice of vehicle height displayed in the cab, in such a manner that it can be easily read by the driver.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (425,1,1,'Permitting another to use a vehicle over 3.66m in height without a notice of vehicle height displayed in the cab, in such a manner that it can be easily read by the driver.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (426,1,1,'Using a vehicle over 3.66m in height without a notice of vehicle height displayed in the cab, in such a manner that it can be easily read by the driver.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (427,1,1,'Causing another to use a low platform trailer in excess of 40 mph.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (428,1,1,'Permitting another to use a low platform trailer in excess of 40 mph.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (429,1,1,'Using restricted speed vehicle in excess of 50 mph','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (430,1,1,'Causing another to use a vehicle with no vehicle identification plate fitted.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (431,1,1,'Exceed maximum permitted laden weight.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (432,1,1,'Using a vehicle with no vehicle identification plate fitted.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (433,1,1,'Causing the use of a vehicle with no ministry plate fitted.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (434,1,1,'Permitting another to use a vehicle with no vehicle identification plate fitted.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (435,1,1,'Permitting the use of a vehicle with no ministry plate fitted.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (436,1,1,'Aid/abet vehicle condition causing danger (Dangerous or defective condition)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (437,1,1,'Aid/abet vehicle condition causing danger (Loads or number of persons)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (438,1,1,'Causing the use of a vehicle - condition causing danger (Loads or number of persons)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (439,1,1,'Causing the use of a vehicle - condition causing danger (Dangerous or defective condition)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (440,1,1,'Causing the use of a vehicle for unsuitable purpose as to cause or be likely to cause danger or nuisance','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (441,1,1,'Permitting the use of a vehicle - condition causing danger (Loads or number of persons)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (442,1,1,'Permitting the use of a vehicle - condition causing danger (Dangerous or defective condition)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (443,1,1,'Using a vehicle - condition causing danger (Dangerous or defective condition)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (444,1,1,'Causing the use of a vehicle with insecure load which is likely to cause danger','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (445,1,1,'Permitting the use of a vehicle carrying load/ appliance/ apparatus the forward projection of which (between 2 and 3.05 metres) is not properly marked and for which no attendant is employed','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (446,1,1,'Permitting the use of a vehicle with insecure load which is likely to cause danger','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (447,1,1,'Using vehicle with insecure load which is likely to cause nuisance','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (448,1,1,'Causing another to use a restricted speed vehicle in excess of 50 mph.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (449,1,1,'Exceeding 70 mph on motorway (offence detected involving the use of camera devices as provided for by Road Traffic Act 1991 s40)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (450,1,1,'Permitting another to use a restricted speed vehicle in excess of 50 mph.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (451,1,1,'Causing a driver not being in position to have full view','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (452,1,1,'Causing the driving of a vehicle with TV set etc visible to driver','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (453,1,1,'Driver not being in position to have full view','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (454,1,1,'Driver not being in position to have proper control','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (455,1,1,'Permitting a driver not being in position to have full view','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (456,1,1,'Permitting a driver not being in position to have proper control','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (457,1,1,'Causing danger of injury due to vehicle condition.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (458,1,1,'Causing another to use a vehicle with any apparatus or appliance fitted for lifting not properly secured.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (459,1,1,'Using a vehicle with any apparatus or appliance fitted for lifting not properly secured.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (460,1,1,'Driving vehicle with TV set etc visible to driver','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (461,1,1,'Permitting the driving of a vehicle with TV set etc visible to driver','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (462,1,1,'Permitting the use of a coach/bus with speed limiter not fitted.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (463,1,1,'Failing to equip trailer with sufficient or suitable spring','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (464,1,1,'Permitting the use of a vehicle with no speed limiter plate fitted.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (465,1,1,'Using vehicle with defective steering','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (466,1,1,'Causing the use of a vehicle with windscreen or window obscuring vision of driver','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (467,1,1,'Permitting the use of a vehicle with windscreen or window obscuring vision of driver','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (468,1,1,'Permitting the use of a vehicle with windscreen wipers not fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (469,1,1,'Causing the use of a goods vehicle (maximum gross weight above 3500 kgs) with mirrors not fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (470,1,1,'Permitting the use of a bus with mirrors not fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (471,1,1,'Permitting the use of a goods vehicle (maximum gross weight above 3500 kgs) with mirrors not fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (472,1,1,'Using bus with mirrors not fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (473,1,1,'Using goods vehicle (maximum gross weight above 3500 kgs) with mirrors not fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (474,1,1,'Using vehicle with windscreen or window obscuring vision of driver','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (475,1,1,'Causing the use of a vehicle with windscreen wipers not fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (476,1,1,'Permitting the use of a vehicle with windscreen washers not fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (477,1,1,'Using vehicle with windscreen wipers not fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (478,1,1,'Causing the use of a vehicle with windscreen washers not fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (479,1,1,'Using vehicle with windscreen washers not fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (480,1,1,'Using vehicle with windscreen wipers/washers not maintained','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (481,1,1,'Causing the use of a vehicle with windscreen wipers/washers not maintained','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (482,1,1,'Permitting the use of a vehicle with windscreen wipers/washers not maintained','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (483,1,1,'Using a vehicle - condition causing danger (Loads or number of persons)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (484,1,1,'Using vehicle with speedometer not fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (485,1,1,'Using vehicle with speedometer obscured','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (486,1,1,'Causing the use of a vehicle not equipped with required mudguards etc','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (487,1,1,'Causing the use of a vehicle with speedometer not maintained','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (488,1,1,'Causing the use of a vehicle with speedometer obscured','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (489,1,1,'Permitting the use of a vehicle with speedometer not maintained','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (490,1,1,'Permitting the use of a vehicle with speedometer obscured','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (491,1,1,'Using vehicle with speedometer not maintained','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (492,1,1,'Causing the use of a coach/bus with defective speed limiter.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (493,1,1,'Speeding with trailer (other than detected involving the use of camera devices under Road Traffic Act 1991 s40)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,15),
    (494,1,1,'Permitting the use of a vehicle which contravenes condition for carrying load/ appliance/ apparatus the forward projection of which is in excess of 3.05 metres','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (495,1,1,'Permitting the use of a vehicle which contravenes condition for carrying load/ appliance/ apparatus the rearward projection of which is in excess of 3.05 metres','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (496,1,1,'Using laden vehicle with width together with lateral projections exceeding 4.3 metres','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (497,1,1,'Using vehicle carrying load/ appliance/ apparatus for the forward projection of which (between 2 and 3.05 metres) no attendant is employed','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (498,1,1,'Using vehicle carrying load/ appliance/ apparatus the forward projection of which (between 2 and 3.05 metres) is not properly marked','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (499,1,1,'Using vehicle carrying load/ appliance/ apparatus the forward projection of which (between 2 and 3.05 metres) is not properly marked and for which no attendant is employed','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (500,1,1,'Using vehicle carrying load/ appliance/ apparatus the rearward projection of which (between 1 and 2 metres) is not rendered clearly visible','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (501,1,1,'Using vehicle carrying load/ appliance/ apparatus the rearward projection of which (between 2 and 3.05 metres) is not properly marked','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (502,1,1,'Using vehicle which contravenes condition for carrying load/ appliance/ apparatus the forward projection of which is in excess of 3.05 metres','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (503,1,1,'Using vehicle which contravenes condition for carrying load/ appliance/ apparatus the rearward projection of which is in excess of 3.05 metres','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (504,1,1,'Unlawfully using wheeled vehicle drawing trailer','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (505,1,1,'Causing another to use a semi-trailer when part of the vehicle was more than 4.2 metres from the ground, while on level ground, and total weight exceeds 32520 kgs.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (506,1,1,'Permitting another to use a semi-trailer when part of the vehicle was more than 4.2 metres from the ground, while on level ground, and total weight exceeds 32520 kgs.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (507,1,1,'Using a semi-trailer when part of a vehicle was more than 4.2 metres from the ground, while on level ground, and total weight exceeds 32520 kgs.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (508,1,1,'Causing use of a trailer/living van to carry passengers.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (509,1,1,'Permitting use of a trailer/living van to carry passengers.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (510,1,1,'Using a trailer/living van to carry passengers.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (511,1,1,'Failing to stop engine etc when stationary.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (512,1,1,'Sounding reverse alarm.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (513,1,1,'Using vehicle with mixed tyres on steerable and driven axles','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (514,1,1,'Causing another to use a passenger vehicle constructed to carry 8 or less seated passengers with defective tyre (where SOW 9a6 applies).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (515,1,1,'Causing the use of a vehicle with no silencer fitted.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (516,1,1,'Permitting another to use a passenger vehicle constructed to carry 8 or less seated passengers with defective tyre (where SOW 9a6 applies).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (517,1,1,'Causing another to use a vehicle (not specified by regulation 15) without a breaking system.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (518,1,1,'Permitting another to use a vehicle (not specified by regulation 15) without a breaking system.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (519,1,1,'Using a vehicle (not specified by regulation 15) without a breaking system.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (520,1,1,'Causing a driver not being in position to have proper control','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (521,1,1,'Permitting the use of a vehicle with defective brakes (Motor vehicle/trailer)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (522,1,1,'Using vehicle with defective brakes (Motor vehicle/trailer)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (523,1,1,'Failing to operate or apply brakes fitted to trailer','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (524,1,1,'Tyres insufficiant to support axle weight.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (525,1,1,'Causing the use of a vehicle with mixed tyres on same axle','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (526,1,1,'Permitting the use of a vehicle with mixed tyres on steerable and driven axles','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (527,1,1,'Using vehicle with mixed tyres on same axle','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (528,1,1,'Causing the use of a vehicle with mixed tyres on front and rear axles','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (529,1,1,'Permitting the use of a vehicle with mixed tyres on same axle','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (530,1,1,'Using vehicle with mixed tyres on front and rear axles','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (531,1,1,'Causing the use of a vehicle with mixed tyres on steerable and driven axles.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (532,1,1,'Using passenger vehicle constructed to carry 8 or less seated passengers with defective tyre (where SOW 9a6 applies)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (533,1,1,'Causing another to use a motor vehicle or trailer with an incorrectly inflated/unsuitable tyre.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (534,1,1,'Causing the use of a motor vehicle or trailer with defective tyre, insufficient tread.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (535,1,1,'Permitting another to use a motor vehicle or trailer with an incorrectly inflated/unsuitable tyre.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (536,1,1,'Permitting the use of a motor vehicle or trailer with defective tyre, insufficient tread.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (537,1,1,'Permitting the use of a vehicle with mixed tyres on front and rear axles','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (538,1,1,'Using a motor vehicle or trailer with an incorrectly inflated/unsuitable tyre.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (539,1,1,'Causing the use of a vehicle with defective steering','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (540,1,1,'Permitting the use of a vehicle with defective steering','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (541,1,1,'Permitting the use of a vehicle with no warning instrument fitted.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (542,1,1,'Causing the use of a locomotive motor tractor or registered heavy motor car with no conspicuous external nearside marking of its unladen weight (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (543,1,1,'Permitting the use of a locomotive motor tractor or registered heavy motor car with no conspicuous external nearside marking of its unladen weight (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (544,1,1,'Using a vehicle with no ministy plate fitted.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (545,1,1,'Causing the use of an unbraked wheeled trailer with no conspicuous external nearside marking of maximum gross weight (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (546,1,1,'Permitting the use of an unbraked wheeled trailer with no conspicuous external nearside marking of maximum gross weight (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (547,1,1,'Using locomotive motor tractor or registered heavy motor car with no conspicuous external nearside marking of its unladen weight (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (548,1,1,'Causing the use of a vehicle exceeding the maximum permitted laden weight.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (549,1,1,'Causing the use of a vehicle with defective brakes (Motor vehicle/trailer)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (550,1,1,'Permitting the use of a vehicle exceeding the maximum permitted laden weight.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (551,1,1,'Allowing total weight of all trailers drawn at any one time by locomotive to exceed 40 650 kilogrammes','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (552,1,1,'Causing the use of a vehicle (other than articulated) exceeding maximum permitted train weight (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (553,1,1,'Using articulated vehicle exceeding maximum permitted laden weight (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (554,1,1,'Using vehicle (other than articulated) exceeding maximum permitted train weight (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (555,1,1,'Causing the use of an articulated vehicle exceeding maximum permitted laden weight (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (556,1,1,'Permitting the use of an articulated vehicle exceeding maximum permitted laden weight (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (557,1,1,'Using vehicle exceeding maximum permitted wheel and axle weights (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (558,1,1,'Causing the use of a vehicle exceeding maximum permitted wheel and axle weights (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (559,1,1,'Permitting the use of a vehicle exceeding maximum permitted wheel and axle weights (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (560,1,1,'Using unbraked wheeled trailer with no conspicuous external nearside marking of maximum gross weight (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (561,1,1,'Exceeding maximum permitted weight for closely-spaced axles.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (562,1,1,'Causing the use of a vehicle exceeding maximum weight shown in manufacturer\'s plate (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (563,1,1,'Permitting the use of a vehicle exceeding maximum weight shown in manufacturer\'s plate (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (564,1,1,'Using vehicle exceeding maximum weight shown in manufacturer\'s plate (goods vehicles and vehicles adapted to carry more than 8 passengers)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (565,1,1,'Using vehicle exceeding maximum weight shown in plating certificate (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (566,1,1,'Causing the use of a vehicle exceeding maximum weight shown in plating certificate (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (567,1,1,'Permitting the use of a vehicle (other than articulated) exceeding maximum permitted train weight (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (568,1,1,'Permitting the use of a vehicle exceeding maximum weight shown in plating certificate (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (569,1,1,'Using vehicle exceeding maximum weight shown in plating certificate (goods vehicles and vehicles adapted to carry more than 8 passengers)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (570,1,1,'Exceeding axle weight as shown on plating certificate','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (571,1,1,'Using unbraked wheeled trailer the laden weight of which exceeds maximum gross weight (goods vehicles and vehicles adapted to carry more than 8 passengers)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (572,1,1,'Using unbraked wheeled trailer the laden weight of which exceeds maximum gross weight (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (573,1,1,'Towing unbraked trailer with towing vehicle less than twice the weight of said trailer and load (goods vehicles and vehicles adapted to carry more than 8 passengers)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (574,1,1,'Towing unbraked trailer with towing vehicle less than twice the weight of said trailer and load (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (575,1,1,'Causing the use of a vehicle so as to cause excessive noise','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (576,1,1,'Transport worker having alcohol level above limit whilst on duty','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (577,1,1,'Using vehicle so as to cause excessive noise .','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (578,1,1,'Failing to display excise licence in prescribed manner','2002-01-22 09:53:03','2002-01-22 09:53:03',1,18),
    (579,1,1,'Failing to produce vehilce for weighing','2002-01-22 09:53:03','2002-01-22 09:53:03',1,18),
    (580,1,1,'Altering vehicle excise licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,18),
    (581,1,1,'Exhibiting document which could be mistaken forr vehicle excise licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,18),
    (582,1,1,'Exhibiting vehicle excise licence which has been altered etc','2002-01-22 09:53:03','2002-01-22 09:53:03',1,18),
    (583,1,1,'Failing to produce registration book','2002-01-22 09:53:03','2002-01-22 09:53:03',1,18),
    (584,1,1,'Causing the use of a vehicle fitted with optional lighting equipment that does not comply with Regulation 20.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (585,1,1,'Permitting the incorrect use of front fog lamp.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (586,1,1,'Permitting the use of a vehicle fitted with optional lighting equipment that does not comply with Regulation 20.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (587,1,1,'Using trailer fitted with optional stop lamp not operated by braking system','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (588,1,1,'Using vehicle fitted with more than 2 optional reversing lamps','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (589,1,1,'Using vehicle fitted with optional dipped-beam headlamp at incorrect height above ground','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (590,1,1,'Using vehicle fitted with optional dipped-beam headlamp incapable of adjustment','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (591,1,1,'Using vehicle fitted with optional dipped-beam headlamp of incorrect colour','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (592,1,1,'Using vehicle fitted with optional dipped-beam headlamp out of alignment','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (593,1,1,'Using vehicle fitted with optional direction indicator having incorrect rate of flashing','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (594,1,1,'Using vehicle fitted with optional direction indicator of incorrect colour','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (595,1,1,'Using vehicle fitted with optional direction indicator of incorrect wattage','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (596,1,1,'Using vehicle fitted with optional direction indicator of insufficient intensity','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (597,1,1,'Using vehicle fitted with optional direction indicator performing inefficiently','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (598,1,1,'Using vehicle fitted with optional direction indicator with incorrect electrical connections','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (599,1,1,'Using vehicle fitted with optional direction indicators flashing out of phase','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (600,1,1,'Using vehicle fitted with optional front position lamp of incorrect colour','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (601,1,1,'Using vehicle fitted with optional main-beam headlamp with incorrect electrical connections','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (602,1,1,'Using vehicle fitted with optional main-beam headlamps incapable of simultaneous use','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (603,1,1,'Using vehicle fitted with optional rear fog lamp of incorrect colour','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (604,1,1,'Using vehicle fitted with optional rear fog lamp operated by braking system','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (605,1,1,'Using vehicle fitted with optional rear position lamp of incorrect colour','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (606,1,1,'Using vehicle fitted with optional rear reflector of incorrect colour','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (607,1,1,'Using vehicle fitted with optional reversing lamp of incorrect colour','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (608,1,1,'Using vehicle fitted with optional reversing lamp of incorrect wattage','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (609,1,1,'Using vehicle fitted with optional stop lamp not operated by braking control','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (610,1,1,'Using vehicle fitted with optional stop lamp not operated by braking system','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (611,1,1,'Using vehicle fitted with optional stop lamp of incorrect colour','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (612,1,1,'Using vehicle fitted with optional stop lamp of incorrect wattage','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (613,1,1,'Using vehicle not equipped with optional tell-tale for direction indicator','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (614,1,1,'Using vehicle with optional direction indicator not fitted so that driver is aware of its operation','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (615,1,1,'Causing the use of a long vehicle or combination of vehicles without the obligatory side marker lamps being fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (616,1,1,'Permitting the use of a long vehicle or combination of vehicles without the obligatory side marker lamps being fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (617,1,1,'Using vehicle fitted with optional lighting equipment that does not comply with Regulation 20.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (618,1,1,'Causing the use of a vehicle not fitted with obligatory lighting equipment','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (619,1,1,'Causing the use of a vehicle not fitted with obligatory lighting equipment.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (620,1,1,'Causing/Using vehicle fitted with obligatory rear fog lamp of incorrect colour','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (621,1,1,'Permitting the use of a vehicle not fitted with obligatory lighting equipment','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (622,1,1,'Permitting the use of a vehicle not fitted with obligatory lighting equipment.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (623,1,1,'Permitting/Using vehicle fitted with obligatory rear fog lamp of incorrect colour','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (624,1,1,'Using a vehicle not fitted with obligatory lighting equipment.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (625,1,1,'Using vehicle with obligatory lamp obscured','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (626,1,1,'Using vehicle with obligatory rear direction indicator fitted to movable vehicle part','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (627,1,1,'Causing the use of a vehicle with obligatory direction indicator obscured','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (628,1,1,'Causing the use of a vehicle with obligatory lamp obscured','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (629,1,1,'Causing the use of a vehicle with obligatory rear reflector obscured','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (630,1,1,'Permitting the use of a vehicle with obligatory direction indicator obscured','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (631,1,1,'Permitting the use of a vehicle with obligatory lamp obscured','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (632,1,1,'Permitting the use of a vehicle with obligatory rear reflector obscured','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (633,1,1,'Permitting the use of lamps not in good working order/clean.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (634,1,1,'Using vehicle with obligatory direction indicator obscured','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (635,1,1,'Using vehicle with obligatory rear reflector obscured','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (636,1,1,'Causing the use of a vehicle fitted with a lateral overhanging load not lit.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (637,1,1,'Permitting the use of a vehicle fitted with a lateral overhanging load not lit.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (638,1,1,'Using a vehicle fitted with a lateral overhanging load not lit.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (639,1,1,'Causing the use of a vehicle fitted with front overhanging load not lit','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (640,1,1,'Driving a vehicle in contravention of a prohibition under ss69 or 70 of this Act','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (641,1,1,'Permitting the use of a vehicle fitted with front overhanging load not lit','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (642,1,1,'Causing the use of a vehicle fitted with side marker lamp of incorrect colour','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (643,1,1,'Causing the use of a vehicle fitted with side marker lamp of insufficient intensity','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (644,1,1,'Permitting the use of a vehicle fitted with side marker lamp of incorrect colour','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (645,1,1,'Permitting the use of a vehicle fitted with side marker lamp of insufficient intensity','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (646,1,1,'Using vehicle fitted with front overhanging load not lit','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (647,1,1,'Using vehicle fitted with side marker lamp of incorrect colour','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (648,1,1,'Causing another to use a vehicle on which a lighting device is not in working order.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (649,1,1,'Causing the use of lamps not in good working order/clean.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (650,1,1,'Permitting another to use a vehicle on which a lighting device is not in working order.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (651,1,1,'Using a vehicle on which a lighting device is not in working order.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (652,1,1,'Using lamps not in good working order/clean.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (653,1,1,'Using long vehicle or combination of vehicles without the obligatory side marker lamps being fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (654,1,1,'Causing another to use a vehicle fitted with rear overhanging load not lit','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (655,1,1,'Permitting another to use a vehicle fitted with rear overhanging load not lit','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (656,1,1,'Using vehicle on which reversing lamp is lit when not reversing','2002-01-22 09:53:03','2002-01-22 09:53:03',1,19),
    (657,1,1,'Sex offences','2002-01-22 09:53:03','2002-01-22 09:53:03',1,20),
    (658,1,1,'Sexual offences','2002-01-22 09:53:03','2002-01-22 09:53:03',1,21),
    (659,1,1,'Failing to cleanse and disinfect road vehicle in which animal transported','2002-01-22 09:53:03','2002-01-22 09:53:03',1,22),
    (660,1,1,'Owner of road vehicle in which animal transported failing to provide all reasonable facilities for cleansing and disinfecting vehicle','2002-01-22 09:53:03','2002-01-22 09:53:03',1,22),
    (661,1,1,'Contravening Inspector\'s prohibition of transporting animal by rail due to illness infirmity injury fatigue or imminent calving foaling etc','2002-01-22  09:53:03','2002-01-22 09:53:03',1,22),
    (662,1,1,'Failing to give name and address and produce insurance to constable after accident/suspected offence on a road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (663,1,1,'Refusing entry to premises for inspection of maintenance facilities','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (664,1,1,'Employer failing to secure return of record sheet within 21 days','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (665,1,1,'Failing to return record sheet to employer within 21 days','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (666,1,1,'Failing to produce record sheet (Employer)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (667,1,1,'Driver taking no weekly rest period of 45, 36 or 24 consecutive hours after 6 daily driving periods.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (668,1,1,'[As added by Road Traffic Act 1974 s16 Sch 4 Para 1] Failing to notify conviction following application for operator\'s licence ','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (669,1,1,'Causing the driver to have a working day in excess of 11 hours.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (670,1,1,'Driving in excess of 10 hours in a working day.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (671,1,1,'Exceeding 90 hours driving in fortnight','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (672,1,1,'Permitting the driver to drive in excess of 10 hours in a working day.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (673,1,1,'Permitting the driver to have a working day in excess of 11 hours.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (674,1,1,'Causing the driver to drive in excess of 10 hours in a working day.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (675,1,1,'Causing a driver not to take minimum weekly rest period of 45, 36 or 24 consecutive hours during the week.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (676,1,1,'Causing a driver to drive in excess of the maximum nine or ten hour daily driving period.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (677,1,1,'Causing a driver to take no weekly rest period of 45, 36 or 24 consecutive hours after 6 daily driving periods.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (678,1,1,'Driver not taking minimum weekly rest period of 45, 36 or 24 consecutive hours during the week.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (679,1,1,'Driving in excess of the maximum nine or ten hour daily driving period.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (680,1,1,'Failure to have a daily period of rest','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (681,1,1,'Permitting a driver not to take minimum weekly rest period of 45, 36 or 24 consecutive hours during the week.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (682,1,1,'Permitting a driver to take no weekly rest period of 45, 36 or 24 consecutive hours after 6 daily driving periods.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (683,1,1,'Caused the driver to drive in excess of 4.5 hours without a minimum break of 45 minutes.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (684,1,1,'Caused the driver to exceed 90 hours driving in a fortnight.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (685,1,1,'Causing another to drive a vehicle manned by more than one driver not to take 8 hours consecutive rest in a period of 30 hours.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (686,1,1,'Causing the driver of a motor vehicle to not have a daily period of rest','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (687,1,1,'Driver of a vehicle manned by more than one driver not taking 8 hours consecutive rest in a period of 30 hours.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (688,1,1,'Driving in excess of 4.5 hours without a minimum break of 45 minutes.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (689,1,1,'Employer failing to issue record book to driver','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (690,1,1,'Permitted the driver to drive in excess of 4.5 hours without a minimum break of 45 minutes.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (691,1,1,'Permitted the driver to exceed 90 hours driving in a fortnight.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (692,1,1,'Permitting a driver to drive in excess of the maximum nine or ten hour daily driving period.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (693,1,1,'Permitting another to drive a vehicle manned by more than one driver not to take 8 hours consecutive rest in a period of 30 hours.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (694,1,1,'Permitting the driver of a motor vehicle to not have a daily period of rest','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (695,1,1,'Obstructing officer in exercise of power of entry','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (696,1,1,'Failing to surrender suspended LGV licence when required','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (697,1,1,'Driver failing to notify employer of existence of another employer','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (698,1,1,'Driver failing to keep proper written record (other than tachograph and timesheet)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (699,1,1,'Employer failing to keep proper written record (other than tachograph and timesheet)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (700,1,1,'Disclosing information about person trade or business after enquiry to which access was restricted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (701,1,1,'Altering record sheet with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (702,1,1,'Altering seal on recording equipment with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (703,1,1,'Permit a person to use a vehicle to which S97 Transport Act applies, without there being recording equipment installed in accordance with the Community Recording Equipment Regulation.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (704,1,1,'Permitting another to use a vehicle with tachograph not functioning correctly/improperly used.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (705,1,1,'Caused a person to use a vehicle to which S97 Transport Act applies, without there being recording equipment installed in accordance with the Community Recording Equipment Regulation.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (706,1,1,'Causing another to use a vehicle with tachograph not functioning correctly/improperly used.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (707,1,1,'Using a vehicle without calibrated recording equipment','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (708,1,1,'Using vehicle after failure to ensure that official time is entered on record sheet','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (709,1,1,'Using vehicle with equipment which was not capable of recording double-manning details','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (710,1,1,'Using vehicle with no tachograph installation plaque','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (711,1,1,'Using vehicle with no tachograph manufacturer\'s plaque','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (712,1,1,'Using vehicle with tachograph seals not intact','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (713,1,1,'Using vehicle without 2 year inspection of tachograph equipment','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (714,1,1,'Employer using vehicle where incorrect record sheet issued','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (715,1,1,'Failing to operate tachograph mode switch','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (716,1,1,'Failing to record period away from vehicle','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (717,1,1,'Failure to use record sheets every day','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (718,1,1,'Using vehicle after failing to enter information concerning name date location vehicle readings etc on record sheet','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (719,1,1,'Using vehicle after failure to attach damaged record sheet to replacement sheet','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (720,1,1,'Using vehicle after failure to protect record sheet from dirt and damage','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (721,1,1,'Using vehicle having failed to check that tachograph functioning correctly','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (722,1,1,'Using vehicle in which record sheet was used beyond specified time','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (723,1,1,'Using vehicle in which tachograph has no record sheet','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (724,1,1,'Using vehicle in which the record sheet for tachograph is dirty or damaged','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (725,1,1,'Forging seal on recording equipment with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (726,1,1,'Making false entry on record sheet','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (727,1,1,'Using seal on recording equipment with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (728,1,1,'Aiding and abetting alteration of record sheet with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (729,1,1,'Aiding and abetting making false entry on record sheet','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (730,1,1,'Failing to produce record sheet (Driver)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (731,1,1,'Using vehicle with tachograph not functioning correctly','2002-01-22 09:53:03','2002-01-22 09:53:03',1,23),
    (732,1,1,'Removing or interfering with an immobilisation notice fixed to a motor vehicle in contravention of a provision included in a charging scheme.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,24),
    (733,1,1,'Removing or interfering with an immobilisation device fixed to a motor vehicle in contravention of a provision included in a charging scheme.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,24),
    (734,1,1,'Intentionally obstructing a person exercising any power conferred on him by provision included in a charging scheme under Part III of this Act','2002-01-22 09:53:03','2002-01-22 09:53:03',1,24),
    (735,1,1,'Intentionally obstructing a person exercising any power conferred on him by a charging scheme under Part III of this Act.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,24),
    (736,1,1,'Being responsible operator of transport system when worker was unfit through drink or drugs','2002-01-22 09:53:03','2002-01-22 09:53:03',1,25),
    (737,1,1,'Being employer of transport system worker who was working whilst unfit through drink or drugs','2002-01-22 09:53:03','2002-01-22 09:53:03',1,25),
    (738,1,1,'Being responsible operator of transport system when worker had alcohol level above prescribed limit','2002-01-22 09:53:03','2002-01-22 09:53:03',1,25),
    (739,1,1,'Being employer of transport system worker who was working with alcohol level above prescribed limit','2002-01-22 09:53:03','2002-01-22 09:53:03',1,25),
    (740,1,1,'Transport worker failing to provide specimen of breath for breath test','2002-01-22 09:53:03','2002-01-22 09:53:03',1,25),
    (741,1,1,'Using or keeping a vehicle on a public road of which vehicle excise duty is chargeable.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (742,1,1,'Licence in force fixed to and exhibited on the vehicle.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (743,1,1,'Keeping vehicle without excise licence in force','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (744,1,1,'Aiding and abetting keeping vehicle without excise licence in force','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (745,1,1,'Aiding and abetting using a vehicle without excise licence in force.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (746,1,1,'Where the vehicle is used so that duty at a higher rate becomes chargeable in respect of the licence for the vehicle under section 15, at any time when the licence is in force.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (747,1,1,'Where a vehicle licence has been taken out for a vehicle at any rate of vehicle excise duty.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (748,1,1,'Driving trailer on which registration mark is not fixed or displayed','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (749,1,1,'Keeping trailer on which registration mark is not fixed or displayed','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (750,1,1,'Keeper of vehicle allegedly contravening s29 of Act failing to give information to Chief of Police about person who kept vehicle on the road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (751,1,1,'Keeper of vehicle allegedly contravening ss 29 34 or 37 of Act failing to give information to Chief of Police about driver or user','2002-01-22 09:53:03','2002- 01-22 09:53:03',1,26),
    (752,1,1,'Person other than keeper of vehicle allegedly contravening ss 29 34 or 37 of Act failing to give information to Chief of Police about driver or user','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (753,1,1,'Person other than keeper or user of vehicle allegedly contravening s29 of Act failing to give information to Chief of Police about person who kept vehicle on the road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (754,1,1,'User of vehicle allegedly contravening s29 of Act failing to give information to Chief of Police about person who kept vehicle on the road','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (755,1,1,'Altering licence with intent to deceive','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (756,1,1,'Forging a vehicle licence, trade licence, registration mark, document.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (757,1,1,'Making false declaration to obtain vehicle or trade excise licence','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (758,1,1,'Making false declaration to obtain rebate','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (759,1,1,'Furnishing false particulars as to keeper of vehicle','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (760,1,1,'Furnishing false particulars as to vehicle','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (761,1,1,'fraudulently altering a vehicle licence, trade licence, registration mark, registration document.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,26),
    (762,1,1,'Causing the use of a coach/bus with speed limiter not fitted.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (763,1,1,'Causing the use of a goods vehicle which was not fitted with speed limiter','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (764,1,1,'Defective speed limiter on coach/bus.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (765,1,1,'Permitting the use of a coach/bus with defective speed limiter.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (766,1,1,'Speed limiter not fitted to a coach/bus.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (767,1,1,'Defective speed limiter on goods vehicle.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (768,1,1,'No speed limiter plate.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (769,1,1,'Permitting the use of a goods vehicle which was not fitted with speed limiter','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (770,1,1,'Permitting the use of a goods vehicle with defective speed limiter.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (771,1,1,'Using goods vehicle which was not fitted with speed limiter','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (772,1,1,'Causing the use of a vehicle with no warning instrument fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (773,1,1,'Causing the use of a vehicle with speedometer not fitted','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (774,1,1,'Using vehicle with no warning instrument fitted ','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (775,1,1,'Causing another to operate a minibus without a fire extinguisher.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (776,1,1,'Permitting another to operate a minibus without a fire extinguisher.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (777,1,1,'Using goods vehicle with no side guards.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (778,1,1,'Failing to maintain sideguards.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (779,1,1,'Causing the use of a vehicle with exhaust gases escaping without passing through silencer.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (780,1,1,'Causing the use of a vehicle with inefficient exhaust system/altered system (not maintained.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (781,1,1,'Permitting the use of a vehicle with exhaust gases escaping without passing through silencer.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (782,1,1,'Permitting the use of a vehicle with no silencer fitted.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (783,1,1,'Using vehicle with exhaust gases escaping without passing through silencer','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (784,1,1,'Using vehicle with no silencer fitted ','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (785,1,1,'Causing the use of a vehicle with inefficient exhaust system/altered system','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (786,1,1,'Permitting the use of a vehicle with inefficient exhaust system/altered system','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (787,1,1,'Permitting the use of a vehicle with inefficient exhaust system/altered system (not maintained)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (788,1,1,'Using vehicle from which emission of smoke/ visible vapour/ grit/ sparks /ashes /cinders /oily substance was causing or was likely to cause damage injury or danger','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (789,1,1,'Using vehicle with altered exhaust system/silencer ','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (790,1,1,'Using vehicle with inefficient exhaust system/altered system (not maintained)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (791,1,1,'Any replacement silencer forming part of the exhaust system of a vehicle to which Regulation 57 applies, being a vehicle first used on or after 1 January 1985, the silencer must meet the first or second requirement set out in 57A(5) & (6).','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (792,1,1,'Exceeding specified limit for exhaust emission','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (793,1,1,'Causing the use of a vehicle from which emission of smoke/ visible vapour/ grit/ sparks /ashes /cinders /oily substance was causing or was likely to cause damage injury or danger','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (794,1,1,'Permitting the use of a vehicle from which emission of smoke/ visible vapour/ grit/ sparks /ashes /cinders /oily substance was causing or was likely to cause damage injury or danger','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (795,1,1,'Permitting the use of a vehicle with unlawful horn fitted.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (796,1,1,'Causing use of diesel engine with excess fuel device not properly maintained.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (797,1,1,'Permitting use of diesel engine with excess fuel device not properly maintained.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (798,1,1,'Using diesel engine with excess fuel device not properly maintained.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (799,1,1,'Permitting the use of a vehicle not equipped with required mudguards etc','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (800,1,1,'Permitting the use of a vehicle so as to cause excessive noise','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (801,1,1,'Using trailer not equipped with required mudguards etc','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (802,1,1,'Using vehicle not equipped with required mudguards etc','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (803,1,1,'Using trailer not fitted with distinguishing mark','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (804,1,1,'Causing another to use a vehicle and trailer of excessive length.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (805,1,1,'Permitting another to use a vehicle and trailer of excessive length.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (806,1,1,'Using a vehicle and trailer of excessive length.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (807,1,1,'Failure to fit Ministry plate to goods vehicle','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (808,1,1,'Failing to display a test date disc on a goods trailer.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (809,1,1,'Causing the use of a laden vehicle with width together with lateral projections exceeding 4.3 metres','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (810,1,1,'Permitting the use of a laden vehicle with width and lateral projections exceeding 2.9 metres but less than 4.3 metres','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (811,1,1,'Permitting the use of a laden vehicle with width together with lateral projections exceeding 4.3 metres','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (812,1,1,'Causing the use of a vehicle with load projection in excess of 305mm','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (813,1,1,'Permitting the use of a vehicle with load projection in excess of 305mm','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (814,1,1,'Using vehicle exceeding maximum weight shown in manufacturer\'s plate (other vehicles)','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (815,1,1,'Causing the use of a laden vehicle with width and lateral projections exceeding 2.9 metres but less than 4.3 metres','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (816,1,1,'Using laden vehicle with width and lateral projections exceeding 2.9 metres but less than 4.3 metres ','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (817,1,1,'Using vehicle with load projection in excess of 305mm','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (818,1,1,'Failing to provide additional employee to attend to vehicle carrying long or wide load','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (819,1,1,'Causing the use of a vehicle carrying load/ appliance/ apparatus the forward projection of which (between 2 and 3.05 metres) is not properly marked and for which no attendant is employed','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (820,1,1,'Causing the use of a vehicle carrying load/ appliance/ apparatus the rearward projection of which (between 1 and 2 metres) is not rendered clearly visible','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (821,1,1,'Causing the use of a vehicle carrying load/ appliance/ apparatus the rearward projection of which (between 2 and 3.05 metres) is not properly marked','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (822,1,1,'Causing the use of a vehicle which contravenes condition for carrying load/ appliance/ apparatus the forward projection of which is in excess of 3.05 metres','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (823,1,1,'Causing the use of a vehicle which contravenes condition for carrying load/ appliance/ apparatus the rearward projection of which is in excess of 3.05 metres','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (824,1,1,'Permitting the use of a vehicle carrying load/ appliance/ apparatus the rearward projection of which (between 1 and 2 metres) is not rendered clearly visible','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (825,1,1,'Permitting the use of a vehicle carrying load/ appliance/ apparatus the rearward projection of which (between 2 and 3.05 metres) is not properly marked','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17),
    (826,1,1,'Causing the use of a goods vehicle with defective speed limiter.','2002-01-22 09:53:03','2002-01-22 09:53:03',1,17);

INSERT INTO `document_sub_category` (`id`, `category_id`, `created_by`, `last_modified_by`, `description`, `is_scanned`,
    `created_on`, `last_modified_on`, `version`) VALUES
    (1,1,NULL,NULL,'Insolvency History',0,NULL,NULL,1),
    (2,1,NULL,NULL,'Advertisement',0,NULL,NULL,1);

SET foreign_key_checks = 1;

DROP TABLE IF EXISTS task_search_view;
DROP VIEW IF EXISTS task_search_view;

CREATE VIEW task_search_view AS
    SELECT t.id, t.assigned_to_team_id, t.assigned_to_user_id, t.action_date, t.urgent,
        t.is_closed, t.category_id, t.task_sub_category_id, t.description,
        cat.description category_name, tsc.name task_sub_category_name,
        coalesce(c.id, br.reg_no, l.lic_no, irfo.id, tm.id, 'Unlinked') id_col,
        coalesce(o.name, irfo.name, tmp.family_name, concat('Case ', c.id), 'Unlinked') name_col,
        l.lic_no, l.id licence_id, irfo.name irfo_op_name, o.name op_name, tmp.family_name, c.id case_id, br.id bus_reg_id,
        u.name user_name, COUNT(ll.id) licence_count
    FROM `task` t

    INNER JOIN (category cat, task_sub_category tsc) ON (cat.id = t.category_id AND tsc.id = t.task_sub_category_id)

    LEFT JOIN licence l ON t.licence_id = l.id

    LEFT JOIN organisation irfo ON t.irfo_organisation_id = irfo.id

    LEFT JOIN organisation o ON l.organisation_id = o.id

    LEFT JOIN licence ll ON (ll.organisation_id = o.id AND (ll.status = 'Valid'))

    LEFT JOIN (transport_manager tm, person tmp, contact_details tmcd)
        ON (t.transport_manager_id = tm.id AND tmp.id = tmcd.person_id AND tmcd.id = tm.contact_details_id)

    LEFT JOIN cases c ON t.case_id = c.id

    LEFT JOIN bus_reg br ON t.bus_reg_id = br.id

    LEFT JOIN user u ON t.assigned_to_user_id = u.id

    GROUP BY (t.id);
