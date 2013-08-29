#!/usr/bin/env python
# -*- coding:utf-8 -*-

import smtplib, sqlite3, logging, ConfigParser, sys
from smtplib import SMTPException
from time import sleep
from email.mime.text import MIMEText
from MF_logger import createLogger

logger = None

class read_task:
    def __init__(self,db_file):
        """
        Arguments:
        - `self`:
        """
        self.db_file = db_file
        self.conn = None

    def conn_db(self,):
        """
        Arguments:
        - `self`:
        """
        if self.conn is None:
            self.conn = sqlite3.connect(self.db_file)

    def dis_conn(self,):
        """
        Arguments:
        - `self`:
        """
        self.conn = None

    def get_tasks(self,):
        """
        Arguments:
        - `self`:
        """
        self.conn_db()
        sql = "SELECT * FROM mail ORDER BY modified DESC LIMIT 10"
        c = self.conn.cursor()
        c.execute(sql)
        return c.fetchall()

    def flush_task(self, id):
        self.conn_db()
        sql = "UPDATE mail SET modified = datetime('NOW()') WHERE id = ?"
        c = self.conn.cursor()
        c.execute(sql, (id,))
        self.conn.commit()

    def task_done(self, id):
        """
        Arguments:
        - `self`:
        """
        self.conn_db()
        sql = "DELETE FROM mail WHERE id = ?"
        c = self.conn.cursor()
        c.execute(sql, (id,))
        self.conn.commit()

class send_email:
    def __init__(self,config_file):
        """

        Arguments:
        - `self`:

        """
        self.smtp = None
        self.config_file = config_file
        self.load_config()
        self.rt = read_task(self.db_file)

    def load_config(self,):
        """
        """
        config = ConfigParser.RawConfigParser()
        config.read(self.config_file)
        section = "config"
        self.server = config.get(section, "server")
        self.port = config.get(section, "port")
        self.sender = config.get(section, "sender")
        self.password = config.get(section, "password")
        self.db_file = config.get(section, "db_file")
        self.log_file = config.get(section, "log_file")
        createLogger(log_file=self.log_file,logger_name="send_mail",log_to_console=False)
        global logger
        logger = logging.getLogger("send_mail")

    def conn_to_server(self,):
        """
        Arguments:
        - `self`:
        """
        self.smtp = None
        try:
            self.smtp = smtplib.SMTP(self.server, self.port)
            self.smtp.ehlo()
            try:
                self.smtp.starttls()
            except SMTPException as e:
                pass
            self.smtp.ehlo()
            self.smtp.login(self.sender, self.password)
        except SMTPException as e:
            logger.debug(e)

    def build_message(self, to, subject, content, mail_type):
        """
        Arguments:
        - `self`:
        - `to`:
        - `subject`:
        - `content`:
        """
        msg = MIMEText(content, mail_type, "utf-8")
        msg['Subject'] = subject
        msg['From'] = self.sender
        msg['To'] = to
        return msg.as_string()

    def _send(self, to, message):
        """
        Arguments:
        - `self`:
        - `to`:
        - `message`:
        """
        if self.smtp is None:
            self.conn_to_server()
        try:
            self.smtp.sendmail(self.sender, to, message)
            return True
        except SMTPException as e:
            if str(e).find("Recipient unknown") != -1 or \
               str(e).find("553") != -1:
                return True
            logger.debug(e)
        return False

    def send(self, task):
        """
        Arguments:
        - `self`:
        - `task`:
        """
        logger.debug("SEND:" + str(task))
        id, to_add, subject, content, mail_type, status, created, modified = task
        message = self.build_message(to_add, subject, content, mail_type)
        to = to_add.strip().strip(",").split(",")
        try:
            if self._send(to, message):
                self.rt.task_done(id)
            else:
                self.rt.flush_task(id)
        except SMTPException as e:
            logger.debug(e)
            return False

    def run(self,):
        """
        Arguments:
        - `self`:
        """
        while True:
            tasks = self.rt.get_tasks()
            if tasks == []:
                sleep(1)
            else:
                for task in tasks:
                    self.send(task)

if __name__ == "__main__":
    send_email(sys.argv[1]).run()
