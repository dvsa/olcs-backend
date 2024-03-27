# Changelog

## [6.0.0](https://github.com/dvsa/olcs-backend/compare/v5.2.0...v6.0.0) (2024-03-27)


### ⚠ BREAKING CHANGES

* interop/container no longer supported
* migrate to GitHub ([#9](https://github.com/dvsa/olcs-backend/issues/9))

### Features

* Allow conversation status filtering ([#114](https://github.com/dvsa/olcs-backend/issues/114)) ([4b4d654](https://github.com/dvsa/olcs-backend/commit/4b4d6546688c45835c46cf3231f9bfba3650e164))
* apply rector 7.4 set ([#117](https://github.com/dvsa/olcs-backend/issues/117)) ([d3b9b13](https://github.com/dvsa/olcs-backend/commit/d3b9b13e4cf0e38ebcaaaa417874d031c0aba792))
* Automatically closed tasks when conversation closed ([#115](https://github.com/dvsa/olcs-backend/issues/115)) ([320f0fb](https://github.com/dvsa/olcs-backend/commit/320f0fb4bffdb085d886987900fce2dc5a3baace))
* Close Conversation FE - add conversation in to result page ([#31](https://github.com/dvsa/olcs-backend/issues/31)) ([dadabf2](https://github.com/dvsa/olcs-backend/commit/dadabf285dcc62a38315458a4fcb0913a2edb7b1))
* Close Handler for Conversations ([#27](https://github.com/dvsa/olcs-backend/issues/27)) ([7062145](https://github.com/dvsa/olcs-backend/commit/7062145390a75ab3b1ddf37f03f0e6d478f7d4c0))
* Composer olcs-transfer Update ([#32](https://github.com/dvsa/olcs-backend/issues/32)) ([a3d3454](https://github.com/dvsa/olcs-backend/commit/a3d34546eb550dde2ebca3cb54811b33b89777d8))
* Conversation by case ([#92](https://github.com/dvsa/olcs-backend/issues/92)) ([ac7623a](https://github.com/dvsa/olcs-backend/commit/ac7623a606f15cd2e8bf0e97728e678f4f47e0cc))
* Conversation subject title on message list ([#85](https://github.com/dvsa/olcs-backend/issues/85)) ([7b91a94](https://github.com/dvsa/olcs-backend/commit/7b91a94560159d79788d0d8eb733b8f7678e44dd))
* CreateMessage command ([#28](https://github.com/dvsa/olcs-backend/issues/28)) ([ff26b13](https://github.com/dvsa/olcs-backend/commit/ff26b1378ec5b690d1e5091aa2c353b45f605eef))
* Custom email for closed and archived conversation ([#96](https://github.com/dvsa/olcs-backend/issues/96)) ([41038d5](https://github.com/dvsa/olcs-backend/commit/41038d5e71a64fe2f2c2eae91c7caace844be80d))
* Enable/Disable Messaging File Upload ([#81](https://github.com/dvsa/olcs-backend/issues/81)) ([17b834d](https://github.com/dvsa/olcs-backend/commit/17b834de84c90fac334f661e21d1d59e20fec24a))
* Enhanced conversation snapshot ([#120](https://github.com/dvsa/olcs-backend/issues/120)) ([c9a0d29](https://github.com/dvsa/olcs-backend/commit/c9a0d29d8dac6f084aaa360d5338ae8c3e139639))
* Filter application options when creating new conversation ([#106](https://github.com/dvsa/olcs-backend/issues/106)) ([8031ae8](https://github.com/dvsa/olcs-backend/commit/8031ae8411a9247befad0f20e59d82f4b5a6414c))
* Handle uploads on new conversation from SS. ([#103](https://github.com/dvsa/olcs-backend/issues/103)) ([67bcb0c](https://github.com/dvsa/olcs-backend/commit/67bcb0c466aa616cf074c5ca31924ff82684e3dc))
* Merge project/messaging ([#72](https://github.com/dvsa/olcs-backend/issues/72)) ([ddeae95](https://github.com/dvsa/olcs-backend/commit/ddeae95a50b10d362006977a70c8311b845af118))
* Message footer showing operator first read ([#110](https://github.com/dvsa/olcs-backend/issues/110)) ([7972680](https://github.com/dvsa/olcs-backend/commit/7972680f2e95f5da2436807e68f5b2e0eeb87b39))
* Message status based on roles not individual users ([#118](https://github.com/dvsa/olcs-backend/issues/118)) ([3055fe9](https://github.com/dvsa/olcs-backend/commit/3055fe9e022d35ed1aeaa8820b202514c517c41d))
* migrate config to application ([#78](https://github.com/dvsa/olcs-backend/issues/78)) ([3db9256](https://github.com/dvsa/olcs-backend/commit/3db9256a3d168e3393a0cd32d1ec5577652b63f5))
* migrate to GitHub ([#9](https://github.com/dvsa/olcs-backend/issues/9)) ([f6025e5](https://github.com/dvsa/olcs-backend/commit/f6025e598484101e81f1532a84e36bcbec23e46b))
* Queries for unread counter ([#79](https://github.com/dvsa/olcs-backend/issues/79)) ([0308842](https://github.com/dvsa/olcs-backend/commit/0308842c912045516579044aee144f2362503a1f))
* Remaining validators and handlers ([#105](https://github.com/dvsa/olcs-backend/issues/105)) ([e98b583](https://github.com/dvsa/olcs-backend/commit/e98b58325dd90c1072d64014fb6f64aa92e9de37))
* remove OpenAM logic ([#109](https://github.com/dvsa/olcs-backend/issues/109)) ([580b86b](https://github.com/dvsa/olcs-backend/commit/580b86bb1ca80d57e3c330b4edf5a13577bd3186))
* replace `laminas-mvc-console` with `laminas-cli` ([#65](https://github.com/dvsa/olcs-backend/issues/65)) ([417be87](https://github.com/dvsa/olcs-backend/commit/417be872d712c4257c98a600790d307f76dc9c47))
* resolve PHPStan issues ([#14](https://github.com/dvsa/olcs-backend/issues/14)) ([d64d93d](https://github.com/dvsa/olcs-backend/commit/d64d93deee8911553ca5aebf817c20bf34d345b4))
* resolve Psalm issues ([#11](https://github.com/dvsa/olcs-backend/issues/11)) ([8c1b2c4](https://github.com/dvsa/olcs-backend/commit/8c1b2c45e8eb978d930021d65b871850248b394d))
* Revealed Caseworker name and added footer text for caseworker messages ([#119](https://github.com/dvsa/olcs-backend/issues/119)) ([9603c93](https://github.com/dvsa/olcs-backend/commit/9603c93a605696994389c68609a861538923e7e5))
* Show message attachments in the internal document list ([#100](https://github.com/dvsa/olcs-backend/issues/100)) ([46922e1](https://github.com/dvsa/olcs-backend/commit/46922e16e572fcc6bb8ee185ec39edd2f6efeadd))
* Subcategory support for task allocation rule and refactors ([#108](https://github.com/dvsa/olcs-backend/issues/108)) ([6cf9481](https://github.com/dvsa/olcs-backend/commit/6cf9481c8d3aee54036eac06d8a429bd98af5985))
* Transport manager deleted/merged search filters ([#124](https://github.com/dvsa/olcs-backend/issues/124)) ([455a8e4](https://github.com/dvsa/olcs-backend/commit/455a8e486e43e6cc3923d2d31132b306cf7511ea))
* Unread Messages for Conversations (By Licence & Roles) ([#88](https://github.com/dvsa/olcs-backend/issues/88)) ([bad7012](https://github.com/dvsa/olcs-backend/commit/bad7012a617db247e101d6bdcac937dd6bec96c4))
* update Doctrine proxy cache directory ([#116](https://github.com/dvsa/olcs-backend/issues/116)) ([6dc4dcf](https://github.com/dvsa/olcs-backend/commit/6dc4dcfaf1a9a0c3c64970436d9fb30a92acddb3))
* Update User Message Reads & General Fixes ([#71](https://github.com/dvsa/olcs-backend/issues/71)) ([0ba8be4](https://github.com/dvsa/olcs-backend/commit/0ba8be462581149217137065bce26686ce50eff9))
* Use Other Documents for Message Archiving ([#76](https://github.com/dvsa/olcs-backend/issues/76)) ([6aaee5b](https://github.com/dvsa/olcs-backend/commit/6aaee5bfdcd132b6ae3adc6d79ee226e3dbd4c2c))
* Use system parameter for GB/NI EMS Email address ([#97](https://github.com/dvsa/olcs-backend/issues/97)) ([3a10b7f](https://github.com/dvsa/olcs-backend/commit/3a10b7f1fb1a5385946ccaadcf0d5ac0046fbe55))
* VOL-3691 switch to Psr Container ([#69](https://github.com/dvsa/olcs-backend/issues/69)) ([310d1db](https://github.com/dvsa/olcs-backend/commit/310d1db1a8447fb4e429cd8452c0a946c84bd5f1))
* VOL-4575 Conversation Message Query ([#20](https://github.com/dvsa/olcs-backend/issues/20)) ([dda85a8](https://github.com/dvsa/olcs-backend/commit/dda85a8621215a384cdc460509cb3c4c8b6be4b5))
* VOL-4994 bus reg expiry batch job now returns number of expired records ([#67](https://github.com/dvsa/olcs-backend/issues/67)) ([59f9959](https://github.com/dvsa/olcs-backend/commit/59f9959b4de27efdaf9be8b24d8f9ea4cb04fd8f))


### Bug Fixes

* add feature toggle functionality to force WebDav ([#25](https://github.com/dvsa/olcs-backend/issues/25)) ([238c862](https://github.com/dvsa/olcs-backend/commit/238c862b3a719574627103d0cbfddee376b2240d))
* Add ROLE_OPERATOR_ADMIN to messaging filtering roles ([#121](https://github.com/dvsa/olcs-backend/issues/121)) ([f2edf8e](https://github.com/dvsa/olcs-backend/commit/f2edf8eda586db63e163769a457d4ae602720e5f))
* assume zero-exit code for print shell commands if not set ([#18](https://github.com/dvsa/olcs-backend/issues/18)) ([097025a](https://github.com/dvsa/olcs-backend/commit/097025af428eaee04f12027ab369b6d213ea453a))
* Block Conversation Close with feature flag ([#36](https://github.com/dvsa/olcs-backend/issues/36)) ([72e15af](https://github.com/dvsa/olcs-backend/commit/72e15af620f02cf1d90b40330b47cb2fae2fe636))
* bump `dvsa/php-govuk-account` ([de862ac](https://github.com/dvsa/olcs-backend/commit/de862ac23388ff5fda5572d3ef51b03f60a329fb))
* Change continuation to conversation in the email body ([#101](https://github.com/dvsa/olcs-backend/issues/101)) ([032e11d](https://github.com/dvsa/olcs-backend/commit/032e11d09a24bf3496bee2ab4458f5b18c2658a0))
* Check for correlation ID for uploads being null ([#89](https://github.com/dvsa/olcs-backend/issues/89)) ([c141574](https://github.com/dvsa/olcs-backend/commit/c14157477e2778a60f84fc2e7abf49d7f27171c1))
* Conversation added to message list page broke test. Refactor of Close missed in map. ([#33](https://github.com/dvsa/olcs-backend/issues/33)) ([cdcda19](https://github.com/dvsa/olcs-backend/commit/cdcda19b8db0edad00b890b9b89a7bc15265c589))
* Correct get call for DoctrineEntityManager ([#58](https://github.com/dvsa/olcs-backend/issues/58)) ([3d7022e](https://github.com/dvsa/olcs-backend/commit/3d7022ef9522ccf018af7eb1f9552b8bf5022ac6))
* correct test data provider method names ([#19](https://github.com/dvsa/olcs-backend/issues/19)) ([898c0f8](https://github.com/dvsa/olcs-backend/commit/898c0f8fe571d34a8095dd0adec5d43c6f61d298))
* fix PHPCS issues ([#10](https://github.com/dvsa/olcs-backend/issues/10)) ([d0a2fa4](https://github.com/dvsa/olcs-backend/commit/d0a2fa449980042d4286a550dabf593968a0cbee))
* fix secret name template ([#111](https://github.com/dvsa/olcs-backend/issues/111)) ([7780e3e](https://github.com/dvsa/olcs-backend/commit/7780e3e56232555205cdbf1fbcbbd36b07292173))
* flag-urgent-tasks batch job getCalls and deprecated doctrine method. ([#61](https://github.com/dvsa/olcs-backend/issues/61)) ([ea20a11](https://github.com/dvsa/olcs-backend/commit/ea20a1150f88124c5b2290d576f30d110150efe5))
* further unit test stability improvements ([#22](https://github.com/dvsa/olcs-backend/issues/22)) ([8dcef51](https://github.com/dvsa/olcs-backend/commit/8dcef5133ede98d5f1f431e20f647891b4ccb6f6))
* improve stability of unit tests ([#21](https://github.com/dvsa/olcs-backend/issues/21)) ([bdd7192](https://github.com/dvsa/olcs-backend/commit/bdd7192cc525d03a58e13d97589491b97031e728))
* Improve TOPS report error logging a bit. ([#60](https://github.com/dvsa/olcs-backend/issues/60)) ([6bfc160](https://github.com/dvsa/olcs-backend/commit/6bfc1609018f99a1da3929f3e6e0c2430cfa5e3e))
* local.php dist missing govuk_account_keys_algorithm ([f6a97bd](https://github.com/dvsa/olcs-backend/commit/f6a97bd6d1a9742a7baa3bd0c5106a84009ba21c))
* re-use Doctrine connection in `AlignEntitiesToSchema` script ([ff60027](https://github.com/dvsa/olcs-backend/commit/ff600279b314fadedb156835eb9584390271b74e))
* redesign query handlers for conversations ([b2f21bc](https://github.com/dvsa/olcs-backend/commit/b2f21bc9aebfce2afe4b2200d9b12203f118296f))
* refactor Laminas deprecations out of unit tests ([#23](https://github.com/dvsa/olcs-backend/issues/23)) ([e494df7](https://github.com/dvsa/olcs-backend/commit/e494df71943af5ed61fa42a623ae661b5ab7d75e))
* Slow join on documents to messages. ([#95](https://github.com/dvsa/olcs-backend/issues/95)) ([2e14f69](https://github.com/dvsa/olcs-backend/commit/2e14f69fc1c0dd519b8a8bc7ac9814f7172621d5))
* subcategory id for licence message ([#75](https://github.com/dvsa/olcs-backend/issues/75)) ([de6a2c4](https://github.com/dvsa/olcs-backend/commit/de6a2c446468d62cfe07a14ac698e1487f467c4c))
* Validator for messaging attachments based on correlation ID ([#122](https://github.com/dvsa/olcs-backend/issues/122)) ([26f503e](https://github.com/dvsa/olcs-backend/commit/26f503e06c6940ad6c999dc1ea72c47db65b393c))
* VOL-4568 - Conversation list ordering fixes ([ce91c1e](https://github.com/dvsa/olcs-backend/commit/ce91c1ef61283d4aa0cfd4e6dcac5e3ed74e9a37))
* VOL-4642 routing in olcs-transfer now sorted out, allowing temporary code to be removed. Code has been reset to how it was previously although tech debt ticket raised as the old code was itself a bit rubbish ([#93](https://github.com/dvsa/olcs-backend/issues/93)) ([5483fcd](https://github.com/dvsa/olcs-backend/commit/5483fcd76c3810d81b1e62c0dfb8f332d07a72e4))
* VOL-4944 update continuation not sought to use bound params and Doctrine Result class ([#52](https://github.com/dvsa/olcs-backend/issues/52)) ([dd0869d](https://github.com/dvsa/olcs-backend/commit/dd0869d2420db82af342d6fcb5c3e487085dc821))
* VOL-4953 remove obsolete companies house method ([#53](https://github.com/dvsa/olcs-backend/issues/53)) ([7a6ffc8](https://github.com/dvsa/olcs-backend/commit/7a6ffc88b23bd1c319a08cec3bc4887fe3ad876b))


### Miscellaneous Chores

* add `.synk` to exclude license issues ([#86](https://github.com/dvsa/olcs-backend/issues/86)) ([5888f6b](https://github.com/dvsa/olcs-backend/commit/5888f6b84735e454147b7433123dde23427968f0))
* add Dependabot config ([#26](https://github.com/dvsa/olcs-backend/issues/26)) ([dee1066](https://github.com/dvsa/olcs-backend/commit/dee1066f5a741a2687d3403f92edf2dc6b72ad7f))
* bump `olcs-transfer` to `v6.2.1` ([#87](https://github.com/dvsa/olcs-backend/issues/87)) ([aab10a0](https://github.com/dvsa/olcs-backend/commit/aab10a0f410a4b8d5c6ecf18c2ec3adebeb0df0f))
* bump `olcs-utils` & `olcs-xmltools` ([#98](https://github.com/dvsa/olcs-backend/issues/98)) ([7ac5cc5](https://github.com/dvsa/olcs-backend/commit/7ac5cc5bd29600c3eb6518f1c0f00ad9a2cbbfdc))
* bump `olcs-utils` to `5.0.0-beta.2` ([#17](https://github.com/dvsa/olcs-backend/issues/17)) ([535e720](https://github.com/dvsa/olcs-backend/commit/535e720ce74767fabf5d33ca71707032a6793dfa))
* bump `phpseclib` to `v2.0.47` ([#90](https://github.com/dvsa/olcs-backend/issues/90)) ([a2cfa40](https://github.com/dvsa/olcs-backend/commit/a2cfa401fc177ba10389e2566960aa4135549b9e))
* bump olcs-utils to v6.2 ([#127](https://github.com/dvsa/olcs-backend/issues/127)) ([34f42cf](https://github.com/dvsa/olcs-backend/commit/34f42cf37b7ab8caaa261c3bf62b6dfb5da3fa93))
* olcs-transfer-6.0.1 ([#82](https://github.com/dvsa/olcs-backend/issues/82)) ([447a71f](https://github.com/dvsa/olcs-backend/commit/447a71ffc95bc7a853369e8c1729506a2b58c3d7))
* Remove fzaninotto/faker dependency ([#129](https://github.com/dvsa/olcs-backend/issues/129)) ([df923b0](https://github.com/dvsa/olcs-backend/commit/df923b0db8081c888520992524e4b3e5f1b31d89))
