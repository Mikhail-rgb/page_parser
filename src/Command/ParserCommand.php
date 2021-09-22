<?php

namespace App\Command;

use App\Entity\Procedure;
use App\Repository\ProcedureRepository;
use DOMAttr;
use DOMDocument;
use DOMXPath;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class ParserCommand extends Command
{
    protected static $defaultName = 'ParserCommand';
    protected static $defaultDescription = 'Add a short description for your command';
    private ProcedureRepository $procedureRepository;

    public function __construct(ProcedureRepository $procedureRepository, bool $requirePassword = false)
    {
        $this->requirePassword = $requirePassword;
        $this->procedureRepository = $procedureRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $content = file_get_contents("https://etp.eltox.ru/registry/procedure/page/1?type=1");

        $doc = new DOMDocument();
        @$doc->loadHTML($content);
        $xpath = new DOMXpath($doc);

        if (!self::parsePage($xpath, $output)) {
            $output->writeln("Can`t parse page #1");
            return Command::FAILURE;
        }

        $amountOfPages = self::getNumberOfLastPage($xpath);
        if ($amountOfPages > 1) {
            for ($page = 2; $page <= $amountOfPages; $page++) {
                $content = file_get_contents("https://etp.eltox.ru/registry/procedure/page/$page?type=1");

                @$doc->loadHTML($content);
                $xpath = new DOMXpath($doc);

                if (!self::parsePage($xpath, $output)) {
                    $output->writeln("Can`t parse page #$page");
                    return Command::FAILURE;
                }
            }
        }

        return Command::SUCCESS;
    }

    private function getNumberOfLastPage(DOMXpath $xpath): int
    {
        $elements = $xpath->query("/html/body/div[3]/div/div/div/div/div[3]/div/div/ul/li[12]/a");

        if (!$elements) {
            return 0;
        }

        return intval($elements->item(0)->textContent);
    }

    private function getProcedureNumber(DOMXpath $xpath, string $procedureXpath): ?int
    {
        $elements = $xpath->query("$procedureXpath/table/tbody/tr[1]/td[2]/dl/dt/a");

        if (!$elements || !is_object($elements->item(0))) {
            return null;
        }

        $procedureNumberStr = $elements->item(0)->nodeValue;

        $procedureNumber = preg_replace("/[^0-9]/", '', $procedureNumberStr);

        return intval($procedureNumber);
    }

    private function getOOSNumber(DOMXpath $xpath, string $procedureXpath): ?int
    {
        $elements = $xpath->query("$procedureXpath/table/tbody/tr[1]/td[2]/dl/dt/span");

        if (!$elements || !is_object($elements->item(0))) {
            return null;
        }

        $OOSNumberStr = $elements->item(0)->nodeValue;

        $OOSNumber = preg_replace("/[^0-9]/", '', $OOSNumberStr);

        return intval($OOSNumber);
    }

    private function getProcedureLink(DOMXpath $xpath, string $procedureXpath): ?string
    {
        $elements = $xpath->query("$procedureXpath/table/tbody/tr[1]/td[2]/dl/dt/a");

        if (!$elements || !is_object($elements->item(0))) {
            return null;
        }

        /**
         * @var DOMAttr $attributes
         */
        $attributes = $elements->item(0)->attributes["href"];

        return $attributes->value;
    }

    private function getEmail(DOMXpath $xpath): ?string
    {
        $emailXpath = "//*[@id=\"tab-basic\"]/table";

        $elements = $xpath->query($emailXpath);

        if (!$elements || !is_object($elements->item(0))) {
            return null;
        }

        $pageText = $elements->item(0)->textContent;

        $startWord = "Почта";
        $finishWord = "Организатор";
        $startPosition = strripos($pageText, $startWord) + strlen($startWord);
        $finishPosition = strripos($pageText, $finishWord);
        $length = $finishPosition - $startPosition;
        $emailStr = substr($pageText, $startPosition, $length);

        return trim($emailStr);
    }

    private function getInfoFromProcedurePage(string $link, Procedure $procedure, OutputInterface $output): Procedure
    {
        $innerContent = file_get_contents($link);
        $innerDoc = new DOMDocument();
        @$innerDoc->loadHTML($innerContent);
        $innerXpath = new DOMXpath($innerDoc);

        $email = self::getEmail($innerXpath);
        if (!$email) {
            $output->writeln("Can`t find email");
        } else {
            $procedure->setEmail($email);
            $output->writeln(sprintf(
                "Email: %s",
                $email
            ));
        }

        return $procedure;
    }

    private function parsePage(DOMXpath $xpath, OutputInterface $output): bool
    {
        $proceduresXpath = "/html/body/div[3]/div/div/div/div/div[2]/div/div[3]/div";
        $mainLink = "https://etp.eltox.ru";
        $elements = $xpath->query($proceduresXpath);

        if (!$elements) {
            return false;
        }

        foreach ($elements as $element) {

            $procedure = new Procedure();

            /**
             * @var DOMAttr $element
             */
            $procedureXpath = $element->getNodePath();

            $procedureNumber = self::getProcedureNumber($xpath, $procedureXpath);
            if (!$procedureNumber) {
                $output->writeln("Can`t find procedure number");
            } else {
                $procedure->setNumber($procedureNumber);
                $output->writeln(sprintf(
                    "Procedure number: %d",
                    $procedureNumber
                ));
            }

            $OOSNumber = self::getOOSNumber($xpath, $procedureXpath);
            if (!$OOSNumber) {
                $output->writeln("Can`t find OOS procedure number");
            } else {
                $procedure->setOosNumber($OOSNumber);
                $output->writeln(sprintf(
                    "OOS procedure number: %d",
                    $OOSNumber
                ));
            }

            $procedureLink = self::getProcedureLink($xpath, $procedureXpath);
            if (!$procedureLink) {
                $output->writeln("Can`t find OOS procedure number");
            } else {
                $fullProcedureLink = $mainLink . $procedureLink;
                $procedure->setLink($fullProcedureLink);
                $output->writeln(sprintf(
                    "Procedure page link: %s",
                    $fullProcedureLink
                ));

                $procedure = self::getInfoFromProcedurePage($fullProcedureLink, $procedure, $output);
            }

            $this->procedureRepository->save($procedure);

            $output->writeln([
                '================',
                ''
            ]);
        }

        return true;
    }
}
