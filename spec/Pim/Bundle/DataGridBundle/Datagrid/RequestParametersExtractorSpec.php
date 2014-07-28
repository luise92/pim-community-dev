<?php

namespace spec\Pim\Bundle\DataGridBundle\Datagrid;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\UserBundle\Context\UserContext;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Oro\Bundle\DataGridBundle\Datagrid\RequestParameters;

class RequestParametersExtractorSpec extends ObjectBehavior
{
    function let(RequestParameters $requestParams) {
        $this->beConstructedWith($requestParams);
    }

    function it_extracts_the_parameter_from_the_datagrid_request(Request $request, $requestParams)
    {
        $this->setRequest($request);
        $requestParams->get('dataLocale', null)->shouldBeCalled()->willReturn('en_US');
        $this->getParameter('dataLocale');
    }

    function it_extracts_the_parameter_from_the_symfony_request(Request $request, $requestParams)
    {
        $this->setRequest($request);
        $requestParams->get('dataLocale', null)->shouldBeCalled();
        $request->get('dataLocale', null)->shouldBeCalled()->willReturn('en_US');
        $this->getParameter('dataLocale');
    }

    function it_trows_a_logic_exception_when_the_parameter_is_not_present(Request $request, $requestParams)
    {
        $this->setRequest($request);
        $requestParams->get('dataLocale', null)->shouldBeCalled();
        $request->get('dataLocale', null)->shouldBeCalled();
        $this->shouldThrow(new \LogicException('Parameter "dataLocale" is expected'))->duringGetParameter('dataLocale');
    }
}
