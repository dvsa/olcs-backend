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
            'type' => 'yesno'
        ),
        'psv_no_small_vhl_confirmation' => array(
            'type' => 'yesno'
        ),
        'psv_limosines' => array(
            'type' => 'yesno'
        ),
        'psv_no_limosine_confirmation' => array(
            'type' => 'yesno'
        ),
        'psv_only_limosines_confirmation' => array(
            'type' => 'yesno'
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
        'requested_by_self_service_user' => array(
            'type' => 'yesno'
        ),
        'reprint_required' => array(
            'type' => 'yesno'
        )
    ),
    'psv_disc' => array(
        'is_copy' => array(
            'type' => 'yesnonull'
        ),
        'reprint_required' => array(
            'type' => 'yesnonull'
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
        'is_tc' => array(
            'type' => 'yesno'
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
        )
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
    'application_completion' => array(
        'section_yb_status' => array(
            'property' => 'sectionYourBusinessStatus'
        ),
        'section_yb_bt_status' => array(
            'property' => 'sectionYourBusinessBusinessTypeStatus'
        ),
        'section_yb_bd_status' => array(
            'property' => 'sectionYourBusinessBusinessDetailsStatus'
        ),
        'section_yb_add_status' => array(
            'property' => 'sectionYourBusinessAddressesStatus'
        ),
        'section_yb_peo_status' => array(
            'property' => 'sectionYourBusinessPeopleStatus'
        ),
        'section_yb_st_status' => array(
            'property' => 'sectionYourBusinessSoleTraderStatus'
        ),
        'section_tol_status' => array(
            'property' => 'sectionTypeOfLicenceStatus'
        ),
        'section_tol_ol_status' => array(
            'property' => 'sectionTypeOfLicenceOperatorLocationStatus'
        ),
        'section_tol_ot_status' => array(
            'property' => 'sectionTypeOfLicenceOperatorTypeStatus'
        ),
        'section_tol_lt_status' => array(
            'property' => 'sectionTypeOfLicenceLicenceTypeStatus'
        ),
        'section_ocs_status' => array(
            'property' => 'sectionOperatingCentresStatus'
        ),
        'section_ocs_auth_status' => array(
            'property' => 'sectionOperatingCentresAuthorisationStatus'
        ),
        'section_ocs_fe_status' => array(
            'property' => 'sectionOperatingCentresFinancialEvidenceStatus'
        ),
        'section_tms_status' => array(
            'property' => 'sectionTransportManagersStatus'
        ),
        'section_tms_p_status' => array(
            'property' => 'sectionTransportManagersPlaceholderStatus'
        ),
        'section_veh_status' => array(
            'property' => 'sectionVehicleSafetyStatus'
        ),
        'section_veh_v_status' => array(
            'property' => 'sectionVehicleSafetyVehicleStatus'
        ),
        'section_veh_vpsv_status' => array(
            'property' => 'sectionVehicleSafetyVehiclePsvStatus'
        ),
        'section_veh_s_status' => array(
            'property' => 'sectionVehicleSafetySafetyStatus'
        ),
        'section_veh_und_status' => array(
            'property' => 'sectionVehicleSafetyUndertakingsStatus'
        ),
        'section_ph_status' => array(
            'property' => 'sectionPreviousHistoryStatus'
        ),
        'section_ph_fh_status' => array(
            'property' => 'sectionPreviousHistoryFinancialHistoryStatus'
        ),
        'section_ph_lh_status' => array(
            'property' => 'sectionPreviousHistoryLicenceHistoryStatus'
        ),
        'section_ph_cp_status' => array(
            'property' => 'sectionPreviousHistoryConvictionsPenaltiesStatus'
        ),
        'section_rd_status' => array(
            'property' => 'sectionReviewDeclarationsStatus'
        ),
        'section_rd_sum_status' => array(
            'property' => 'sectionReviewDeclarationsSummaryStatus'
        ),
        'section_pay_status' => array(
            'property' => 'sectionPaymentSubmissionStatus'
        ),
        'section_pay_pay_status' => array(
            'property' => 'sectionPaymentSubmissionPaymentStatus'
        ),
        'section_pay_summary_status' => array(
            'property' => 'sectionPaymentSubmissionSummaryStatus'
        ),
        'section_tp_status' => array(
            'property' => 'sectionTaxiPhvStatus'
        ),
        'section_tp_lic_status' => array(
            'property' => 'sectionTaxiPhvLicenceStatus'
        )
    ),
    'presiding_tc' => array(
        'deleted' => array(
            'type' => 'yesnonull'
        )
    ),
    'condition_undertaking' => array(
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
        )
    ),
    'previous_licence' => array(
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
        )
    ),
    'si_penalty' => array(
        'imposed' => array(
            'type' => 'yesnonull'
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
        'is_representation' => array(
            'type' => 'yesno'
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
    )
);
