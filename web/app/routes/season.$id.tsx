import { LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";

export const loader = ({ params }: LoaderFunctionArgs) => {
  const seasonId = params.id;
  return seasonId;
};

export default function Season() {
  const seasonId = useLoaderData<typeof loader>();
  return <div>{seasonId}</div>;
}
