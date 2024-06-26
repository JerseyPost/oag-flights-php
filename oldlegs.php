// Load the Routes file
$body = json_decode('{"legs":[
    "JER-LHR",
    "LHR-BEY",
    "BEY-DXB",
    "LHR-DXB",
    "DXB-BEY",
    "STH-LAL",
    "JER-ORD",
    "ORD-AXA",
    "LHR-TIA",
    "LHR-BEG",
    "BEG-TIA",
    "ORD-LHR",
    "LAX-LHR",
    "MIA-LHR",
    "EWR-LHR",
    "BEY-EVN",
    "LHR-EVN",
    "LHR-LAD",
    "LHR-LIS",
    "LIS-LAD",
    "LHR-BUE",
    "STH-HOU",
    "HOU-RTM",
    "RTM-VLM",
    "LHR-VIE",
    "STH-NYS",
    "NYS-XWC",
    "LHR-BNE",
    "LHR-SYD",
    "LHR-AUA",
    "LHR-BAK",
    "LHR-SJJ",
    "BEG-SJJ",
    "LHR-BGI",
    "LHR-DAC",
    "LHR-IST",
    "IST-DAC",
    "DAC-IST",
    "LAL-NYS",
    "NYS-BRU",
    "HOU-AMS",
    "AMS-BRU",
    "LHR-BRU",
    "LHR-OUA",
    "LHR-ADD",
    "ADD-OUA",
    "LHR-WAW",
    "WAW-SOF",
    "LHR-SOF",
    "NYS-PNZ",
    "LHR-BAH",
    "ADD-BJM",
    "LHR-BJM",
    "ADD-COO",
    "LHR-COO",
    "ORD-BDA",
    "ORD-CBB",
    "LHR-GRU",
    "LIS-GRU",
    "ORD-PBH",
    "LHR-GBE",
    "ADD-GBE",
    "LHR-YUL",
    "LHR-YYZ",
    "ADD-FIH",
    "LHR-FIH",
    "ADD-BZV",
    "LHR-BZV",
    "HOU-URD",
    "LHR-ZRH",
    "BEY-ABJ",
    "LHR-ABJ",
    "ORD-RAR",
    "LHR-SCL",
    "YYZ-SCL",
    "ADD-DLA",
    "LHR-DLA",
    "STN-CAN",
    "LHR-CAN",
    "LHR-BJS",
    "YYZ-BOG",
    "LHR-BOG",
    "LHR-SJO",
    "LHR-CUR",
    "LHR-LCA",
    "LHR-ATH",
    "ATH-LCA",
    "LIS-PRG",
    "NYS-PRG",
    "LHR-PRG",
    "HOU-FRA",
    "LHR-FRA",
    "NYS-FRA",
    "ADD-JIB",
    "LHR-JIB",
    "NYS-CPH",
    "LIS-CPH",
    "LHR-CPH",
    "LHR-SDQ",
    "LHR-ALG",
    "IST-ALG",
    "YYZ-UIO",
    "LGW-TLL",
    "LHR-TLL",
    "NYS-LOO",
    "BEY-CAI",
    "LHR-CAI",
    "CAI-BEY",
    "LHR-ASM",
    "LHR-MAD",
    "HOU-MAD",
    "NYS-MAD",
    "LHR-HEL",
    "LGW-RIX",
    "RIX-HEL",
    "NYS-HEI",
    "ORD-NAN",
    "HOU-ROI",
    "LHR-CDG",
    "ADD-LBV",
    "LHR-LBV",
    "LHR-CVT",
    "ORD-GND",
    "LHR-TBS",
    "BEY-ACC",
    "LHR-ACC",
    "LHR-GIB",
    "LHR-BJL",
    "LHR-CKY",
    "YUL-PTP",
    "LHR-SSG",
    "ADD-SSG",
    "NYS-SPT",
    "LHR-GUA",
    "LHR-HKG",
    "ORD-TGU",
    "LHR-ZAG",
    "NYS-ZAG",
    "ORD-PAP",
    "LHR-BUD",
    "ZHR-BUD",
    "NYS-BUE",
    "IST-CGK",
    "LHR-CGK",
    "HOU-COL",
    "LHR-DUB",
    "NYS-COL",
    "LHR-TLV",
    "LHR-BOM",
    "WAW-BOM",
    "LHR-DEL",
    "BEY-BGW",
    "LHR-BGW",
    "LHR-REK",
    "STN-KEF",
    "NYS-MIL",
    "HOU-MIL",
    "LHR-MXP",
    "HOU-STH",
    "LHR-KIN",
    "AMM-BEY",
    "BEY-AMM",
    "LHR-AMM",
    "ORD-DOH",
    "DOH-NRT",
    "LHR-HND",
    "LHR-NBO",
    "LHR-FRU",
    "DOH-PNH",
    "LHR-PNH",
    "ORD-CXI",
    "LHR-SEL",
    "DOH-ICN",
    "ICN-WAW",
    "WAW-ICN",
    "BEY-KWI",
    "LHR-KWI",
    "LHR-ALA",
    "ORD-SLU",
    "LHR-CMB",
    "CAI-JNB",
    "NYS-VRN",
    "RIX-VNO",
    "LHR-VNO",
    "RTM-SWL",
    "LHR-LUX",
    "LHR-RIX",
    "NYS-RJN",
    "LHR-CAS",
    "LIS-CMN",
    "LHR-CAS ",
    "WAW-KIV",
    "LHR-KIV",
    "BEG-TGD",
    "LHR-TGD",
    "LHR-TNR",
    "LHR-SKP",
    "BEG-SKP",
    "ADD-BKO",
    "LHR-BKO",
    "LHR-RGN",
    "ORD-ULN",
    "DOH-MFM",
    "LHR-MFM",
    "LHR-NKC",
    "ZRH-MLA",
    "LHR-MLA",
    "LHR-MRU",
    "IST-MLE",
    "DOH-MLE",
    "LHR-MLE",
    "ADD-LLW",
    "LHR-LLW",
    "YYZ-MEX",
    "LHR-MEX",
    "LHR-KUL",
    "ADD-MPM",
    "LHR-MPM",
    "ADD-WDH",
    "WDH-ADD",
    "LHR-WDH",
    "ORD-NOU",
    "ADD-NIM",
    "LHR-NIM",
    "BEY-LOS",
    "LHR-LOS",
    "ORD-MGA",
    "LHR-HAG",
    "NYS-WAW",
    "OSL-RIX",
    "RIX-OSL",
    "HOU-OSL",
    "LHR-OSL",
    "LHR-KTM",
    "LHR-AKL",
    "LHR-MCT",
    "LHR-PTY",
    "YYZ-LIM",
    "LHR-LIM",
    "ORD-POM",
    "LHR-MNL",
    "LHR-ISB",
    "DOH-ISB",
    "NYS-LIS",
    "BEY-DOH",
    "LHR-DOH",
    "LHR-RUN",
    "LHR-BUH",
    "WAW-OTP",
    "NYS-BUD",
    "WAW-BEG",
    "ADD-KGL",
    "LHR-KGL",
    "LHR-DMM",
    "LHR-SEZ",
    "DOH-SEZ",
    "ADD-SEZ",
    "HOU-OSB",
    "LHR-ARN",
    "NYS-STO",
    "LHR-SIN",
    "LHR-SIN ",
    "DOH-SIN",
    "HOU-LJB",
    "LHR-LJU",
    "LHR-BTS",
    "LIS-DSS",
    "LHR-DKR",
    "LHR-SAL",
    "ORD-GDT",
    "ADD-NDJ",
    "LHR-NDJ",
    "ADD-LFW",
    "LHR-LFW",
    "LHR-BKK",
    "LHR-TUN",
    "TUN-IST",
    "IST-TUN",
    "ORD-TBU",
    "BEY-IST",
    "IST-ATH",
    "ATH-IST",
    "LHR-POS",
    "ORD-FUN",
    "LHR-TPE",
    "IST-TPE",
    "LHR-DAR",
    "ADD-DAR",
    "ADD-EBB",
    "LHR-EBB",
    "LHR-JFK",
    "ZRH-JFK",
    "LHR-MVD",
    "ORD-MVD",
    "LHR-TAS",
    "IST-TAS",
    "ORD-MAR",
    "IST-HAN",
    "LHR-HAN",
    "ORD-VLI",
    "ORD-APW",
    "LHR-JNB",
    "LHR-CPT",
    "ADD-LUN",
    "LHR-LUN",
    "ADD-HRE",
    "LHR-HRE"
]}');
*/