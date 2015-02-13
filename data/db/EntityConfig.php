<?php

return array(
    'organisation' => array(
        'is_mlh' => array(
            'type' => 'yesno'
        ),
        'company_cert_seen' => array(
            'type' => 'yesno'
        ),
        'is_irfo' => array(
            'type' => 'yesno'
        ),
        'allow_email' => array(
            'type' => 'yesno'
        )
    ),
    'organisation_nature_of_business' => array(
        'organisation_id' => array(
            'inversedBy' => array(
                'entity' => 'Organisation',
                'property' => 'natureOfBusiness'
            ),
        )
    ),
    'country' => array(
        'is_member_state' => array(
            'type' => 'yesno'
        )
    ),
    'address' => array(
        'paon_desc' => array(
            'property' => 'addressLine2'
        ),
        'saon_desc' => array(
            'property' => 'addressLine1'
        ),
        'street' => array(
            'property' => 'addressLine3'
        ),
        'locality' => array(
            'property' => 'addressLine4'
        )
    ),
    'contact_details' => array(
        'licence_id' => array(
            'inversedBy' => array(
                'entity' => 'Licence',
                'property' => 'contactDetail'
            )
        ),
        'organisation_id' => array(
            'inversedBy' => array(
                'entity' => 'Organisation',
                'property' => 'contactDetail'
            )
        ),
        'address_id' => array(
            'inversedBy' => array(
                'entity' => 'Address',
                'property' => 'contactDetail'
            ),
            'cascade' => array(
                'persist'
            )
        ),
        'person_id' => array(
            'cascade' => array(
                'persist'
            ),
            'inversedBy' => array(
                'entity' => 'Person',
                'property' => 'contactDetail'
            )
        ),
        'written_permission_to_engage' => array(
            'type' => 'yesno'
        ),
        'phone_contacts' => array(
            'cascade' => array(
                'persist'
            ),
            'inversedBy' => array(
                'entity' => 'PhoneContact',
                'property' => 'contactDetails'
            )
        )
    ),
    'user' => array(
        'account_disabled' => array(
            'type' => 'yesno'
        )
    ),
    'licence' => array(
        'organisation_id' => array(
            'inversedBy' => array(
                'entity' => 'Organisation',
                'property' => 'licence'
            )
        ),
        'safety_ins' => array(
            'type' => 'yesno'
        ),
        'safety_ins_varies' => array(
            'type' => 'yesnonull'
        ),
        'ni_flag' => array(
            'type' => 'yesnonull'
        ),
        'translate_to_welsh' => array(
            'type' => 'yesno'
        ),
        'is_maintenance_suitable' => array(
            'type' => 'yesnonull'
        )
    ),
    'application' => array(
        'ni_flag' => array(
            'type' => 'yesnonull'
        ),
        'licence_id' => array(
            'inversedBy' => array(
                'entity' => 'Licence',
                'property' => 'application'
            )
        ),
        'has_entered_reg' => array(
            'type' => 'yesnonull'
        ),
        'bankrupt' => array(
            'type' => 'yesnonull'
        ),
        'administration' => array(
            'type' => 'yesnonull'
        ),
        'disqualified' => array(
            'type' => 'yesnonull'
        ),
        'liquidation' => array(
            'type' => 'yesnonull'
        ),
        'receivership' => array(
            'type' => 'yesnonull'
        ),
        'insolvency_confirmation' => array(
            'type' => 'yesno'
        ),
        'safety_confirmation' => array(
            'type' => 'yesno'
        ),
        'declaration_confirmation' => array(
            'type' => 'yesno'
        ),
        'prev_has_licence' => array(
            'type' => 'yesnonull'
        ),
        'prev_had_licence' => array(
            'type' => 'yesnonull'
        ),
        'prev_been_refused' => array(
            'type' => 'yesnonull'
        ),
        'prev_been_revoked' => array(
            'type' => 'yesnonull'
        ),
        'prev_been_at_pi' => array(
            'type' => 'yesnonull'
        ),
        'prev_been_disqualified_tc' => array(
            'type' => 'yesnonull'
        ),
        'prev_purchased_assets' => array(
            'type' => 'yesnonull'
        ),
        'override_ooo' => array(
            'type' => 'yesno'
        ),
        'prev_conviction' => array(
            'type' => 'yesnonull'
        ),
        'convictions_confirmation' => array(
            'type' => 'yesno'
        ),
        'psv_operate_small_vhl' => array(
            'type' => 'yesnonull'
        ),
        'psv_small_vhl_confirmation' => array(
            'type' => 'yesnonull'
        ),
        'psv_no_small_vhl_confirmation' => array(
            'type' => 'yesnonull'
        ),
        'psv_limousines' => array(
            'type' => 'yesnonull'
        ),
        'psv_no_limousine_confirmation' => array(
            'type' => 'yesnonull'
        ),
        'psv_only_limousines_confirmation' => array(
            'type' => 'yesnonull'
        ),
        'is_maintenance_suitable' => array(
            'type' => 'yesnonull'
        )
    ),
    's4' => array(
        'surrender_licence' => array(
            'type' => 'yesno'
        ),
        'is_true_s4' => array(
            'type' => 'yesno'
        )
    ),
    'licence_operating_centre' => array(
        'ad_placed' => array(
            'type' => 'yesno'
        ),
        'sufficient_parking' => array(
            'type' => 'yesno'
        ),
        'permission' => array(
            'type' => 'yesno'
        ),
        'is_interim' => array(
            'type' => 'yesnonull'
        ),
        'publication_appropriate' => array(
            'type' => 'yesnonull'
        ),
        'licence_id' => array(
            'inversedBy' => array(
                'entity' => 'Licence',
                'property' => 'operatingCentre'
            )
        )
    ),
    'application_operating_centre' => array(
        'ad_placed' => array(
            'type' => 'yesno'
        ),
        'publication_appropriate' => array(
            'type' => 'yesno'
        ),
        'permission' => array(
            'type' => 'yesno'
        ),
        'sufficient_parking' => array(
            'type' => 'yesno'
        ),
        'is_interim' => array(
            'type' => 'yesno'
        ),
        'application_id' => array(
            'inversedBy' => array(
                'entity' => 'Application',
                'property' => 'operatingCentre'
            )
        )
    ),
    'document_sub_category' => array(
        'is_scanned' => array(
            'type' => 'yesno'
        ),
        'display_free_text' => array(
            'type' => 'yesno'
        )
    ),
    'cases' => array(
        'licence_id' => array(
            'inversedBy' => array(
                'entity' => 'Licence',
                'property' => 'case'
            )
        ),
        'is_impounding' => array(
            'type' => 'yesno'
        )
    ),
    'opposition' => array(
        'is_representation' => array(
            'type' => 'yesno'
        ),
        'is_copied' => array(
            'type' => 'yesno'
        ),
        'is_in_time' => array(
            'type' => 'yesno'
        ),
        'is_public_inquiry' => array(
            'type' => 'yesno'
        ),
        'is_valid' => array(
            'type' => 'yesno'
        ),
        'is_withdrawn' => array(
            'type' => 'yesno'
        ),
        'is_willing_to_attend_pi' => array(
            'type' => 'yesno'
        ),
        'application_id' => array(
            'inversedBy' => array(
                'entity' => 'Application',
                'property' => 'opposition'
            )
        ),
        'case_id' => array(
            'inversedBy' => array(
                'entity' => 'Cases',
                'property' => 'opposition'
            )
        ),
        'opposer_id' => array(
            'cascade' => array(
                'persist'
            )
        )
    ),
    'operating_centre_opposition' => array(
        'opposition_id' => array(
            'inversedBy' => array(
                'entity' => 'Opposition',
                'property' => 'operatingCentre'
            )
        )
    ),
    'opposer' => array(
        'contact_details_id' => array(
            'cascade' => array(
                'persist'
            )
        )
    ),
    'bus_reg' => array(
        'use_all_stops' => array(
            'type' => 'yesno'
        ),
        'is_short_notice' => array(
            'type' => 'yesno'
        ),
        'has_manoeuvre' => array(
            'type' => 'yesno'
        ),
        'need_new_stop' => array(
            'type' => 'yesno'
        ),
        'has_not_fixed_stop' => array(
            'type' => 'yesno'
        ),
        'timetable_acceptable' => array(
            'type' => 'yesno'
        ),
        'map_supplied' => array(
            'type' => 'yesno'
        ),
        'copied_to_la_pte' => array(
            'type' => 'yesno'
        ),
        'la_short_note' => array(
            'type' => 'yesno'
        ),
        'application_signed' => array(
            'type' => 'yesno'
        ),
        'op_notified_la_pte' => array(
            'type' => 'yesno'
        ),
        'trc_condition_checked' => array(
            'type' => 'yesno'
        ),
        'is_txc_app' => array(
            'type' => 'yesno'
        ),
        'short_notice_refused' => array(
            'type' => 'yesno'
        ),
        'is_quality_partnership' => array(
            'type' => 'yesno'
        ),
        'quality_partnership_facilities_used' => array(
            'type' => 'yesno'
        ),
        'is_quality_contract' => array(
            'type' => 'yesno'
        ),
    ),
    'bus_reg_other_service' => array(
        'bus_reg_id' => array(
            'inversedBy' => array(
                'entity' => 'BusReg',
                'property' => 'otherService'
            )
        )
    ),
    'document' => array(
        'document_store_id' => array(
            'property' => 'identifier'
        ),
        'traffic_area_id' => array(
            'inversedBy' => array(
                'entity' => 'TrafficArea',
                'property' => 'document'
            )
        ),
        'is_read_only' => array(
            'type' => 'yesnonull'
        ),
        'licence_id' => array(
            'inversedBy' => array(
                'entity' => 'Licence',
                'property' => 'document'
            )
        ),
        'application_id' => array(
            'inversedBy' => array(
                'entity' => 'Application',
                'property' => 'document'
            )
        ),
        'case_id' => array(
            'inversedBy' => array(
                'entity' => 'Cases',
                'property' => 'document'
            )
        ),
        'transport_manager_id' => array(
            'inversedBy' => array(
                'entity' => 'TransportManager',
                'property' => 'document'
            )
        ),
        'operating_centre_id' => array(
            'inversedBy' => array(
                'entity' => 'OperatingCentre',
                'property' => 'adDocument'
            )
        ),
        'opposition_id' => array(
            'inversedBy' => array(
                'entity' => 'Opposition',
                'property' => 'document'
            )
        ),
        'bus_reg_id' => array(
            'inversedBy' => array(
                'entity' => 'BusReg',
                'property' => 'document'
            )
        )
    ),
    'doc_template' => array(
        'is_ni' => array(
            'type' => 'yesno'
        ),
        'suppress_from_op' => array(
            'type' => 'yesno'
        )
    ),
    'doc_template_bookmark' => array(
        'doc_template_id' => array(
            'inversedBy' => array(
                'entity' => 'DocTemplate',
                'property' => 'docTemplateBookmark'
            )
        )
    ),
    'doc_paragraph_bookmark' => array(
        'doc_bookmark_id' => array(
            'inversedBy' => array(
                'entity' => 'DocBookmark',
                'property' => 'docParagraphBookmark'
            )
        )
    ),
    'email' => array(
        'is_sensitive' => array(
            'type' => 'yesnonull'
        )
    ),
    'vehicle' => array(
        'is_articulated' => array(
            'type' => 'yesnonull'
        ),
        'is_refrigerated' => array(
            'type' => 'yesnonull'
        ),
        'is_tipper' => array(
            'type' => 'yesnonull'
        ),
        'is_novelty' => array(
            'type' => 'yesnonull'
        ),
        'section26' => array(
            'type' => 'yesno'
        ),
        'section26_curtail' => array(
            'type' => 'yesno'
        ),
        'section26_revoked' => array(
            'type' => 'yesno'
        ),
        'section26_suspend' => array(
            'type' => 'yesno'
        )
    ),
    'licence_vehicle' => array(
        'licence_id' => array(
            'inversedBy' => array(
                'entity' => 'Licence',
                'property' => 'licenceVehicle'
            )
        ),
        'application_id' => array(
            'inversedBy' => array(
                'entity' => 'Application',
                'property' => 'licenceVehicle'
            )
        ),
        'vehicle_id' => array(
            'inversedBy' => array(
                'entity' => 'Vehicle',
                'property' => 'licenceVehicle'
            )
        ),
        'removal' => array(
            'type' => 'yesnonull'
        )
    ),
    'goods_disc' => array(
        'is_copy' => array(
            'type' => 'yesno'
        ),
        'is_interim' => array(
            'type' => 'yesno'
        ),
        'is_printing' => array(
            'type' => 'yesno'
        ),
        'requested_by_self_service_user' => array(
            'type' => 'yesno'
        ),
        'reprint_required' => array(
            'type' => 'yesno'
        ),
        'licence_vehicle_id' => array(
            'inversedBy' => array(
                'entity' => 'LicenceVehicle',
                'property' => 'goodsDisc',
                'orderBy' => array(
                    'createdOn' => 'DESC'
                )
            )
        )
    ),
    'psv_disc' => array(
        'is_copy' => array(
            'type' => 'yesnonull'
        ),
        'is_printing' => array(
            'type' => 'yesno'
        ),
        'reprint_required' => array(
            'type' => 'yesnonull'
        ),
        'licence_id' => array(
            'inversedBy' => array(
                'entity' => 'Licence',
                'property' => 'psvDisc',
                'orderBy' => array(
                    'discNo' => 'ASC'
                )
            )
        )
    ),
    'organisation_person' => array(
        'organisation_id' => array(
            'inversedBy' => array(
                'entity' => 'Organisation',
                'property' => 'organisationPerson'
            )
        )
    ),
    'trading_name' => array(
        'organisation_id' => array(
            'inversedBy' => array(
                'entity' => 'Organisation',
                'property' => 'tradingName'
            )
        )
    ),
    'workshop' => array(
        'licence_id' => array(
            'inversedBy' => array(
                'entity' => 'Licence',
                'property' => 'workshop'
            )
        ),
        'is_external' => array(
            'type' => 'yesno'
        ),
        'maintenance' => array(
            'type' => 'yesno'
        ),
        'safety_inspection' => array(
            'type' => 'yesno'
        ),
    ),
    'stay' => array(
        'case_id' => array(
            'inversedBy' => array(
                'entity' => 'Cases',
                'property' => 'stay'
            )
        ),
        'is_tc' => array(
            'type' => 'yesno'
        )
    ),
    'appeal' => array(
        'case_id' => array(
            'inversedBy' => array(
                'entity' => 'Cases',
                'property' => 'appeal'
            )
        )
    ),
    'complaint' => array(
        'complainant_contact_details_id' => array(
            'cascade' => array(
                'persist'
            )
        ),
        'driver_id' => array(
            'cascade' => array(
                'persist'
            )
        ),
        'case_id' => array(
            'inversedBy' => array(
                'entity' => 'Cases',
                'property' => 'complaint'
            )
        )
    ),
    'oc_complaint' => array(
        'complaint_id' => array(
            'inversedBy' => array(
                'entity' => 'Complaint',
                'property' => 'ocComplaint'
            )
        )
    ),
    'complaint_case' => array(
        'complaint_id' => array(
            'cascade' => array(
                'persist',
                'remove'
            )
        ),
        'case_id' => array(
            'cascade' => array(
                'persist'
            ),
            'inversedBy' => array(
                'entity' => 'Cases',
                'property' => 'complaintCase'
            )
        )
    ),
    'prohibition' => array(
        'is_trailer' => array(
            'type' => 'yesnonull'
        ),
        'case_id' => array(
            'inversedBy' => array(
                'entity' => 'Cases',
                'property' => 'prohibition'
            )
        ),
    ),
    'conviction' => array(
        'msi' => array(
            'type' => 'yesnonull'
        ),
        'is_declared' => array(
            'type' => 'yesno'
        ),
        'case_id' => array(
            'inversedBy' => array(
                'entity' => 'Cases',
                'property' => 'conviction'
            )
        ),
        'application_id' => array(
            'inversedBy' => array(
                'entity' => 'Application',
                'property' => 'conviction'
            )
        ),
        'is_dealt_with' => array(
            'type' => 'yesno'
        )
    ),
    'irfo_gv_permit' => array(
        'is_fee_exempt' => array(
            'type' => 'yesno'
        ),
        'permit_printed' => array(
            'type' => 'yesno'
        )
    ),
    'irfo_psv_auth' => array(
        'is_fee_exempt_application' => array(
            'type' => 'yesno'
        ),
        'is_fee_exempt_annual' => array(
            'type' => 'yesno'
        )
    ),
    'note' => array(
        'priority' => array(
            'type' => 'yesno'
        )
    ),
    'submission_action' => array(
        'is_decision' => array(
            'type' => 'yesno'
        ),
        'urgent' => array(
            'type' => 'yesnonull'
        ),
        'submission_id' => array(
            'inversedBy' => array(
                'entity' => 'Submission',
                'property' => 'submissionAction'
            )
        )
    ),
    'submission_section_comment' => array(
        'submission_id' => array(
            'inversedBy' => array(
                'entity' => 'Submission',
                'property' => 'submissionSectionComment'
            )
        )
    ),
    'application_completion' => array(
        'application_id' => array(
            'inversedBy' => array(
                'entity' => 'Application',
                'property' => 'applicationCompletion'
            )
        )
    ),
    'presiding_tc' => array(
        'deleted' => array(
            'type' => 'yesnonull'
        )
    ),
    'condition_undertaking' => array(
        'application_id' => array(
            'inversedBy' => array(
                'entity' => 'Application',
                'property' => 'conditionUndertaking'
            )
        ),
        'licence_id' => array(
            'inversedBy' => array(
                'entity' => 'Licence',
                'property' => 'conditionUndertaking'
            )
        ),
        'case_id' => array(
            'inversedBy' => array(
                'entity' => 'Cases',
                'property' => 'conditionUndertaking'
            )
        ),
        'is_draft' => array(
            'type' => 'yesno'
        ),
        'is_fulfilled' => array(
            'type' => 'yesno'
        ),
        'is_approved' => array(
            'type' => 'yesno'
        ),
        'lic_condition_variation_id' => array(
            'inversedBy' => array(
                'entity' => 'ConditionUndertaking',
                'property' => 'variationRecord'
            )
        )
    ),
    'previous_licence' => array(
        'application_id' => array(
            'inversedBy' => array(
                'entity' => 'Application',
                'property' => 'previousLicence'
            )
        ),
        'will_surrender' => array(
            'type' => 'yesnonull'
        )
    ),
    'organisation_user' => array(
        'user_id' => array(
            'inversedBy' => array(
                'entity' => 'User',
                'property' => 'organisationUser'
            )
        ),
        'is_administrator' => array(
            'type' => 'yesno'
        ),
        'sftp_access' => array(
            'type' => 'yesno'
        )
    ),
    'reason' => array(
        'is_decision' => array(
            'type' => 'yesno'
        ),
        'is_read_only' => array(
            'type' => 'yesno'
        ),
        'is_ni' => array(
            'type' => 'yesno'
        ),
        'is_propose_to_revoke' => array(
            'type' => 'yesno'
        )
    ),
    'pi' => array(
        'is_cancelled' => array(
            'type' => 'yesno'
        ),
        'is_adjourned' => array(
            'type' => 'yesno'
        ),
        'licence_revoked_at_pi' => array(
            'type' => 'yesno'
        ),
        'licence_suspended_at_pi' => array(
            'type' => 'yesno'
        ),
        'licence_curtailed_at_pi' => array(
            'type' => 'yesno'
        )
    ),
    'legacy_case_action' => array(
        'is_driver' => array(
            'type' => 'yesno'
        )
    ),
    'legacy_recommendation' => array(
        'revoke_lic' => array(
            'type' => 'yesnonull'
        ),
        'status' => array(
            'type' => 'yesnonull'
        )
    ),
    'pi_definition' => array(
        'is_ni' => array(
            'type' => 'yesno'
        )
    ),
    'task' => array(
        'is_closed' => array(
            'type' => 'yesno'
        ),
        'urgent' => array(
            'type' => 'yesno'
        )
    ),
    'inspection_email' => array(
        'processed' => array(
            'type' => 'yesno'
        )
    ),
    'bus_reg_traffic_area' => array(
        'txc_missing' => array(
            'type' => 'yesnonull'
        ),
        'txc_not_required' => array(
            'type' => 'yesnonull'
        )
    ),
    'bus_short_notice' => array(
        'bank_holiday_change' => array(
            'type' => 'yesno'
        ),
        'unforseen_change' => array(
            'type' => 'yesno'
        ),
        'timetable_change' => array(
            'type' => 'yesno'
        ),
        'replacement_change' => array(
            'type' => 'yesno'
        ),
        'holiday_change' => array(
            'type' => 'yesno'
        ),
        'trc_change' => array(
            'type' => 'yesno'
        ),
        'police_change' => array(
            'type' => 'yesno'
        ),
        'special_occasion_change' => array(
            'type' => 'yesno'
        ),
        'connection_change' => array(
            'type' => 'yesno'
        ),
        'not_available_change' => array(
            'type' => 'yesno'
        )
    ),
    'recipient' => array(
        'send_app_decision' => array(
            'type' => 'yesno'
        ),
        'send_notices_procs' => array(
            'type' => 'yesno'
        ),
        'is_police' => array(
            'type' => 'yesno'
        ),
        'is_objector' => array(
            'type' => 'yesno'
        )
    ),
    'fee_type' => array(
        'expire_fee_with_licence' => array(
            'type' => 'yesno'
        )
    ),
    'waive_reason' => array(
        'is_irfo' => array(
            'type' => 'yesno'
        )
    ),
    'fee' => array(
        'irfo_fee_exempt' => array(
            'type' => 'yesnonull'
        )
    ),
    'ebsr_submission_result' => array(
        'email_authority' => array(
            'type' => 'yesno'
        )
    ),
    'ebsr_submission' => array(
        'is_from_ftp' => array(
            'type' => 'yesno'
        )
    ),
    'alpha_split' => array(
        'is_deleted' => array(
            'type' => 'yesno'
        )
    ),
    'application_action_ref' => array(
        'default_received' => array(
            'type' => 'yesnonull'
        ),
        'default_approved' => array(
            'type' => 'yesnonull'
        )
    ),
    'application_action' => array(
        'is_received' => array(
            'type' => 'yesnonull'
        ),
        'is_approved' => array(
            'type' => 'yesnonull'
        ),
        'is_applicable' => array(
            'type' => 'yesnonull'
        )
    ),
    'serious_infringement' => array(
        'erru_response_sent' => array(
            'type' => 'yesno'
        ),
        'case_id' => array(
            'inversedBy' => array(
                'entity' => 'Cases',
                'property' => 'seriousInfringement'
            )
        )
    ),
    'si_penalty' => array(
        'imposed' => array(
            'type' => 'yesnonull'
        ),
        'serious_infringement_id' => array(
            'inversedBy' => array(
                'entity' => 'SeriousInfringement',
                'property' => 'appliedPenaltie'
            )
        )
    ),
    'si_penalty_erru_imposed' => array(
        'serious_infringement_id' => array(
            'inversedBy' => array(
                'entity' => 'SeriousInfringement',
                'property' => 'imposedErru'
            )
        ),
        'executed' => array(
            'yesno'
        )
    ),
    'si_penalty_erru_requested' => array(
        'serious_infringement_id' => array(
            'inversedBy' => array(
                'entity' => 'SeriousInfringement',
                'property' => 'requestedErru'
            )
        )
    ),
    'correspondence_inbox' => array(
        'archived' => array(
            'type' => 'yesnonull'
        ),
        'accessed' => array(
            'type' => 'yesnonull'
        ),
        'email_reminder_sent' => array(
            'type' => 'yesnonull'
        ),
        'printed' => array(
            'type' => 'yesnonull'
        )
    ),
    'disc_sequence' => array(
        'is_self_serve' => array(
            'type' => 'yesno'
        ),
        'is_ni_self_serve' => array(
            'type' => 'yesno'
        )
    ),
    'disqualification' => array(
        'is_disqualified' => array(
            'type' => 'yesnonull'
        )
    ),
    'legacy_offence' => array(
        'is_trailer' => array(
            'type' => 'yesnonull'
        )
    ),
    'community_lic_suspension' => array(
        'is_actioned' => array(
            'type' => 'yesnonull'
        )
    ),
    'irfo_vehicle' => array(
        'coc_a' => array(
            'type' => 'yesno'
        ),
        'coc_b' => array(
            'type' => 'yesno'
        ),
        'coc_c' => array(
            'type' => 'yesno'
        ),
        'coc_d' => array(
            'type' => 'yesno'
        ),
        'coc_t' => array(
            'type' => 'yesno'
        )
    ),
    'system_info_message' => array(
        'is_internal' => array(
            'type' => 'yesno'
        ),
        'is_deleted' => array(
            'type' => 'yesno'
        )
    ),
    'opposition_grounds' => array(
        'opposition_id' => array(
            'inversedBy' => array(
                'entity' => 'Opposition',
                'property' => 'ground'
            )
        )
    ),
    'tm_case_decision' => array(
        'is_msi' => array(
            'type' => 'yesno'
        )
    ),
    'tm_grace_period' => array(
        'is_active' => array(
            'type' => 'yesno'
        )
    ),
    'txc_inbox' => array(
        'file_read' => array(
            'type' => 'yesnonull'
        )
    ),
    'public_holiday' => array(
        'is_england' => array(
            'type' => 'yesnonull'
        ),
        'is_wales' => array(
            'type' => 'yesnonull'
        ),
        'is_scotland' => array(
            'type' => 'yesnonull'
        ),
        'is_ni' => array(
            'type' => 'yesnonull'
        )
    ),
    'driver' => array(
        'contact_details_id' => array(
            'cascade' => array(
                'persist'
            )
        )
    ),
    'pi_hearing' => array(
        'is_adjourned' => array(
            'type' => 'yesno'
        ),
        'pi_id' => array(
            'inversedBy' => array(
                'entity' => 'Pi',
                'property' => 'piHearing'
            )
        ),
        'is_cancelled' => array(
            'type' => 'yesno'
        ),
        'is_adjourned' => array(
            'type' => 'yesno'
        )
    ),
    'pi_reason' => array(
        'pi_id' => array(
            'inversedBy' => array(
                'entity' => 'Pi',
                'property' => 'piReason'
            )
        )
    ),
    /* 'pi_type' => array(
      'pi_id' => array(
      'inversedBy' => array(
      'entity' => 'Pi',
      'property' => 'piType'
      )
      )
      ) , */
    'category' => array(
        'is_doc_category' => array(
            'type' => 'yesno'
        ),
        'is_task_category' => array(
            'type' => 'yesno'
        )
    ),
    'task_sub_category' => array(
        'is_freetext_description' => array(
            'type' => 'yesno'
        )
    ),
    'ref_data' => array(
        '@settings' => array(
            'repository' => 'Olcs\Db\Entity\Repository\RefData'
        ),
        'description' => array(
            'translatable' => true
        )
    ),
    'ext_translations' => array(
        '@settings' => array(
            'ignore' => true
        )
    ),
    'phone_contact' => array(
        'contact_details_id' => array(
            'inversedBy' => array(
                'entity' => 'ContactDetails',
                'property' => 'phoneContact',
                'cascade' => array(
                    'persist'
                )
            )
        )
    ),
    'previous_conviction' => array(
        'application_id' => array(
            'inversedBy' => array(
                'entity' => 'Application',
                'property' => 'previousConviction'
            )
        )
    ),
    'private_hire_licence' => array(
        'licence_id' => array(
            'inversedBy' => array(
                'entity' => 'Licence',
                'property' => 'privateHireLicence'
            )
        )
    ),
    'fee_payment' => array(
        'fee_id' => array(
            'inversedBy' => array(
                'entity' => 'Fee',
                'property' => 'feePayment'
            )
        )
    ),
    'tm_application_oc' => array(
        'transport_manager_application_id' => array(
            'inversedBy' => array(
                'entity' => 'TransportManagerApplication',
                'property' => 'tmApplicationOc'
            ),
        )
    ),
    'tm_licence_oc' => array(
        'transport_manager_licence_id' => array(
            'inversedBy' => array(
                'entity' => 'TransportManagerLicence',
                'property' => 'tmLicenceOc'
            ),
        )
    ),
    'transport_manager_licence' => array(
        'licence_id' => array(
            'inversedBy' => array(
                'entity' => 'Licence',
                'property' => 'tmLicence'
            ),
        )
    ),
    'transport_manager_application' => array(
        'application_id' => array(
            'inversedBy' => array(
                'entity' => 'Application',
                'property' => 'tmApplication'
            ),
        )
    ),
    'tm_qualification' => array(
        'transport_manager_id' => array(
            'inversedBy' => array(
                'entity' => 'TransportManager',
                'property' => 'qualification'
            ),
        )
    ),
    'other_licence' => array(
        'transport_manager_id' => array(
            'inversedBy' => array(
                'entity' => 'TransportManager',
                'property' => 'otherLicence'
            ),
        )
    ),
    'publication_link' => array(
        'pi_id' => array(
            'inversedBy' => array(
                'entity' => 'Pi',
                'property' => 'publicationLink'
            )
        ),
        'application_id' => array(
            'inversedBy' => array(
                'entity' => 'Application',
                'property' => 'publicationLink'
            )
        )
    ),
    'role_permission' => array(
        'role_id' => array(
            'inversedBy' => array(
                'entity' => 'Role',
                'property' => 'rolePermission'
            )
        )
    ),
    'transport_manager_application' => array(
        'application_id' => array(
            'inversedBy' => array(
                'entity' => 'Application',
                'property' => 'transportManager'
            )
        )
    ),
    'statement' => array(
        'case_id' => array(
            'inversedBy' => array(
                'entity' => 'Cases',
                'property' => 'statement'
            ),
        ),
        'requestors_contact_details_id' => array(
            'cascade' => array(
                'persist'
            )
        )
    ),
    'community_lic' => array(
        'licence_id' => array(
            'inversedBy' => array(
                'entity' => 'Licence',
                'property' => 'communityLic'
            )
        ),
    )
);
