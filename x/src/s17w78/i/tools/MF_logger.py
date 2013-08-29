#! /usr/bin/env python
# -*- coding: utf-8 -*-

from datetime import date
import logging

def createLogger(log_file=None, logger_name=None, format=False, log_to_console=False, get_hdlr=False):
    today_date = date.today()
    if log_file == None:
        log_file = "/data0/moofayii/logs/log_debug_" + str(today_date) + ".log"
    elif not log_file.startswith("/"):
        log_file = "/data0/moofayii/logs/"+log_file + "_debug_" + str(today_date) + ".log"

    if logger_name == None:
        logger_name = "def_logger"

    logger = logging.getLogger(logger_name)

    if log_to_console:
        hdlr = logging.StreamHandler()
    else:
        hdlr = logging.FileHandler(log_file)

    if format:
        formatter = logging.Formatter('%(name)-12s: %(levelname)-8s %(message)s')
        hdlr.setFormatter(formatter)

    logger.addHandler(hdlr)
    logger.setLevel(logging.DEBUG)
    if get_hdlr:
        return hdlr

def closeHdlr(hdlr, logger_name):
    hdlr.flush()
    hdlr.close()
    logging.getLogger(logger_name).removeHandler(hdlr)
    return
