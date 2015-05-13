SET foreign_key_checks = 0;

START TRANSACTION;

TRUNCATE TABLE `queue`;
TRUNCATE TABLE `companies_house_officer`;
TRUNCATE TABLE `companies_house_company`;

INSERT INTO `queue` (`status`, `type`, `options`) VALUES
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00000133"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00000687"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00000950"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00001160"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00001978"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00003671"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00004600"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00004606"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00006005"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00006278"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00006470"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00006480"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00009117"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00009551"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00010139"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00010994"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00011116"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00011771"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00017652"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00018712"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00020535"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00021054"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00021576"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00021607"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00021886"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00022041"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00022456"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00022537"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00023891"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00025850"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00026373"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00028073"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00028203"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00029131"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00029224"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00029409"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00029559"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00030048"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00030563"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00031438"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00031641"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00031754"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00031801"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00031916"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00032543"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00033217"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00033527"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00034195"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00034273"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00035025"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00035049"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00036822"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00038357"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00038597"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00038755"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00040987"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00041942"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00042015"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00042732"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00043026"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00043765"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00044259"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00046572"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00046723"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00046833"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00047094"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00048111"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00048519"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00048629"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00048669"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00048988"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00049488"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00050159"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00050234"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00050470"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00050806"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00050955"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00051828"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00052016"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00052111"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00052581"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00053268"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00054056"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00054288"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00054643"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00055030"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00055247"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00055252"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00055569"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00055803"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00055936"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00056605"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00057244"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00057762"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00057987"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00059225"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00059245"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00059375"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00060351"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00060795"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00061083"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00061141"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00061272"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00061652"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00061890"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00062178"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00063031"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00063606"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00063739"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00064404"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00064584"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00064795"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00065805"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00065957"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00065986"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00068098"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00068589"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00068757"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00068890"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00069556"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00069606"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00070368"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00071434"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00071835"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00072097"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00072101"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00072114"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00072188"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00072727"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00073396"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00073785"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00074434"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00075016"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00075746"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00077653"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00078950"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00079678"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00080437"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00080715"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00081035"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00081567"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00082562"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00082788"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00082908"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00083575"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00083597"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00083824"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00084511"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00084638"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00084758"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00085006"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00085074"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00085133"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00085308"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00085951"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00086661"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00087084"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00087089"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00087227"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00087997"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00088166"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00089725"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00090041"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00090627"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00090670"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00090708"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00091050"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00091182"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00091219"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00091580"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00091741"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00091783"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00092501"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00092565"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00092580"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00092589"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00093058"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00094305"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00094369"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00095559"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00095883"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00095951"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00096979"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00097547"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00097849"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00098080"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00098220"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00098291"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00098677"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00098737"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"00099241"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1821RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1835RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1847RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1850RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1861RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1864RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1868RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1870RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1879RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1880RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1883RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1887RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1893RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1917RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1921RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1947RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1953RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1957RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1962RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1966RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1976RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1992RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP1997RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2000RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2001RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2002RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2003RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2004RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2044RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2049RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2072RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2073RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2126RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2127RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2148RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2150RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2154RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2237RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2336RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2354RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2374RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2411RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2428RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2455RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2471RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2483RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2489RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2499RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2544RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2549RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2581RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2585RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2619RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2635RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2669RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"SP2684RS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"STEWARTS SWIFTS"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"ZC000143"}'),
 ('que_sts_queued', 'que_typ_ch_initial', '{"companyNumber":"ZC000154"}');

COMMIT;

SET foreign_key_checks = 1;